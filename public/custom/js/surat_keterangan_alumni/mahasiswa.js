(function() {
    'use strict';

    // Check if Laravel routes are properly defined
    if (!window.Laravel || !window.Laravel.listData) {
        console.error('Laravel routes are not properly defined');
        return;
    }

    // First, define all your functions at the top
    const initializeDataTable = () => {
        return $("#ska-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: window.Laravel.listData,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr) {
                    console.error('DataTables AJAX error:', xhr.responseText);
                    let msg = 'Terjadi kesalahan saat memuat data.';
                    try { var res = JSON.parse(xhr.responseText); if (res.error) msg = res.error || res.message; } catch(e){}
                    Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                }
            },
        columns: [
            { data: 'created_at', visible: false },
            { data: 'DT_RowIndex' },
            { data: 'nama' },
            { data: 'nim' },
            { data: 'program_studi' },
            { data: 'tanggal_lulus' },
            { data: 'nomor_ijazah' },
            { data: 'status' },
            { data: 'tanggal_submit', name: 'created_at' },
            { data: 'file' },
            { data: 'action' }
        ],
        columnDefs: [
            { className: 'text-center', width: '3%', targets: [1] },
            { className: 'text-center', targets: [3,6,7,8,9] }
        ],
        order: [[0, 'desc']]
    });
};

// Then initialize your variables and event handlers
let table;

if ($("#ska-datatable").length) {
    table = initializeDataTable();
}

// No queue polling — controller does not supply queue_number. Removed periodic queue-status checks.

// detail
$('#show_data').on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        var url = window.Laravel.getData.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if (res.status) {
                    $('#detail-nama').html(': ' + (res.data.user ? res.data.user.name : '-'));
                    $('#detail-nim').html(': ' + (res.data.user ? res.data.user.nim : '-'));
                    $('#detail-prodi').html(': ' + (res.data.user && res.data.user.prodis ? res.data.user.prodis.nama : '-'));
                    $('#detail-permohonan').html(': ' + (res.data.permohonan || '-'));
                    $('#detail-tanggal_lulus').html(': ' + (res.data.tanggal_lulus || '-'));
                    $('#detail-nomor_ijazah').html(': ' + (res.data.nomor_ijazah || '-'));

                    if (res.data.file_url) {
                        $('#detail-file').attr('href', res.data.file_url).removeClass('disabled').removeAttr('aria-disabled');
                    } else {
                        $('#detail-file').attr('href', '#').addClass('disabled').attr('aria-disabled', 'true');
                    }

                    $('#modalDetail').modal('show');
                } else {
                    Swal.fire({ title: 'Error', text: res.message, icon: 'error' });
                }
            },
            error: function(xhr) {
                console.error('Detail AJAX error:', xhr.responseText);
                var msg = 'Terjadi kesalahan saat memuat detail';
                try { var err = JSON.parse(xhr.responseText); if (err.message) msg = err.message; } catch(e){}
                Swal.fire({ title: 'Error', text: msg, icon: 'error' });
            }
        });
    });

// edit
$('#show_data').on('click', '.btn-edit', function() {
    let id = $(this).data('id');

    let action = window.Laravel.updateData.replace(':id', id);
    let url = window.Laravel.getData.replace(':id', id);

    $.ajax({
        url: url,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (res) {
            if (res.status) {
                $('form#form-edit').attr('action', action);
                $('#form-edit textarea[name="permohonan"]').val(res.data.permohonan || '');
                $('#form-edit input[name="tanggal_lulus"]').val(res.data.tanggal_lulus);
                $('#form-edit input[name="nomor_ijazah"]').val(res.data.nomor_ijazah || '');

                $('#modalEdit').modal('show');
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: res.message,
                    icon: 'error',
                });
            }
        },
        error: function (xhr, status, error) {
            var err = JSON.parse(xhr.responseText);
            Swal.fire({
                title: 'Error!',
                text: err.message,
                icon: 'error',
            });
        },
    });
});

// delete
$('#show_data').on('click', '.btn-delete', function() {
        var $btn = $(this);
        Swal.fire({
            title: 'Anda yakin membatalkan ajuan?',
            text: 'Ajuan yang dibatalkan tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Batalkan Ajuan!'
        }).then(function(result){
            if (!result.isConfirmed) return;
            var id = $btn.data('id');
            var url = window.Laravel.deleteData.replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: function(){ Swal.fire({ title: 'Mohon Tunggu', allowOutsideClick: false, didOpen: function(){ Swal.showLoading(); } }); },
                success: function(res){ Swal.close(); if (res.status) { Swal.fire({ title: 'Berhasil!', text: res.message, icon: 'success' }); table.ajax.reload(); } else { Swal.fire({ title: 'Gagal!', text: res.message, icon: 'error' }); } },
                error: function(xhr){ console.error('Delete error:', xhr.responseText); var msg = 'Terjadi kesalahan'; try { var e = JSON.parse(xhr.responseText); if (e.message) msg = e.message; } catch(e){} Swal.fire({ title: 'Gagal!', text: msg, icon: 'error' }); }
            });
        });
    });

// form tambah with client-side checks and better 422 handling
$('#form-tambah').submit(function(e){
        e.preventDefault();
        var form = this;

        // basic client-side validation to avoid sending empty payload
        var missing = [];
        ['permohonan','nomor_ijazah','tanggal_lulus'].forEach(function(name){
            var $el = $(form).find('[name="' + name + '"]');
            if (!$el.val() || $el.val().toString().trim() === '') missing.push(name);
        });

        if (missing.length) {
            Swal.fire({ title: 'Field required', text: 'Mohon isi: ' + missing.join(', '), icon: 'warning' });
            return;
        }

        Swal.fire({ title: 'Apakah inputan Anda sudah benar?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Sudah!' }).then(function(result){
            if (!result.isConfirmed) return;
            var formData = new FormData(form);
            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: function(){ Swal.fire({ title: 'Mohon Tunggu', allowOutsideClick:false, didOpen: function(){ Swal.showLoading(); } }); },
                success: function(res){
                    Swal.close();
                    if (res.status) {
                        $(form).trigger('reset');
                        $('#modalTambah').modal('hide');
                        Swal.fire({ title: 'Berhasil!', text: res.message || 'Ajuan terkirim', icon: 'success' });
                        table.ajax.reload();
                    } else {
                        Swal.fire({ title: 'Gagal!', text: res.message || 'Terjadi kesalahan', icon: 'error' });
                    }
                },
                error: function(xhr){
                    Swal.close();
                    console.error('Form error:', xhr.responseText);
                    if (xhr.status === 422) {
                        // Laravel validation errors
                        var errors = {};
                        try { errors = JSON.parse(xhr.responseText).errors || {}; } catch(e){}
                        var msgs = Object.values(errors).map(function(v){ return v.join(' '); }).join('\n');
                        Swal.fire({ title: 'Validasi Gagal', html: msgs || 'Harap periksa inputan Anda', icon: 'warning' });
                    } else {
                        var msg = 'Terjadi kesalahan';
                        try { var e = JSON.parse(xhr.responseText); if (e.message) msg = e.message; } catch(e){}
                        Swal.fire({ title: 'Gagal!', text: msg, icon: 'error' });
                    }
                }
            });
        });
    });

// form edit
$('#form-edit').submit(function(e){
    e.preventDefault();
    var form = this;

    Swal.fire({
        title: 'Apakah inputan Anda sudah benar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Sudah!'
    }).then(function(result){
        if (!result.isConfirmed) return;
        var formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend: function(){
                Swal.fire({
                    title: 'Mohon Tunggu',
                    allowOutsideClick:false,
                    didOpen: function(){
                        Swal.showLoading();
                    }
                });
            },
            success: function(res){
                Swal.close();
                if (res.status) {
                    $(form).trigger('reset');
                    $('#modalEdit').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: 'success'
                    });
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: res.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr){
                Swal.close();
                console.error('Edit error:', xhr.responseText);

                if (xhr.status === 422) {
                    // Laravel validation errors
                    var errors = {};
                    try {
                        errors = JSON.parse(xhr.responseText).errors || {};
                    } catch(e){}
                    var msgs = Object.values(errors).map(function(v){
                        return v.join(' ');
                    }).join('\n');
                    Swal.fire({
                        title: 'Validasi Gagal',
                        html: msgs || 'Harap periksa inputan Anda',
                        icon: 'warning'
                    });
                } else {
                    var msg='Terjadi kesalahan';
                    try{
                        var e = JSON.parse(xhr.responseText);
                        if (e.message) msg = e.message;
                    } catch(e){}
                    Swal.fire({
                        title:'Gagal!',
                        text: msg,
                        icon:'error'
                    });
                }
            }
        });
    });
});

})();
