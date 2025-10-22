(function() {
    'use strict';

    // Check if Laravel routes are properly defined
    if (!window.Laravel || !window.Laravel.listData) {
        console.error('Laravel routes are not properly defined');
        return;
    }

    // First, define all your functions at the top
    const initializeDataTable = () => {
        return $("#sr-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: window.Laravel.listData,
                type: 'GET',
                dataType: 'json',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                error: function(xhr) {
                    console.error('DataTables AJAX error:', xhr.responseText);
                    let msg = 'Terjadi kesalahan saat memuat data.';
                    try { var res = JSON.parse(xhr.responseText); if (res.error) msg = res.error; if (res.message) msg = res.message; } catch(e){}
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
            { data: 'tanggal_submit' },
            { data: 'file' },
            { data: 'action' },
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

if ($("#sr-datatable").length) {
    table = initializeDataTable();
}

// No queue polling — controller does not provide queue_number.

$("#show_data").on("click", ".btn-edit", function () {
    let id = $(this).data("id");

    let action = window.Laravel.updateData.replace(":id", id);
    let url = window.Laravel.getData.replace(":id", id);

    $.ajax({
        url: url,
        type: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (res.status) {
                $("form#form-edit").attr("action", action);
                $("#form-edit textarea[name='permohonan']").val(res.data.permohonan || '');
                $("#form-edit input[name='tanggal_lulus']").val(res.data.tanggal_lulus || '');
                $("#form-edit input[name='nomor_ijazah']").val(res.data.nomor_ijazah || '');

                $("#modalEdit").modal("show");
            } else {
                Swal.fire({
                    title: "Error!",
                    text: res.message,
                    icon: "error",
                });
            }
        },
        error: function (xhr, status, error) {
            var err = JSON.parse(xhr.responseText);
            Swal.fire({
                title: "Error!",
                text: err.message,
                icon: "error",
            });
        },
    });
});

$("#show_data").on("click", ".btn-detail", function () {
    let id = $(this).data("id");

    let url = window.Laravel.getData.replace(":id", id);

    $.ajax({
        url: url,
        type: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (res.status) {
                $("#detail-nama").html(": " + (res.data.user ? res.data.user.name : '-'));
                $("#detail-nim").html(": " + (res.data.user ? res.data.user.nim : '-'));
                $("#detail-prodi").html(": " + (res.data.user && res.data.user.prodis ? res.data.user.prodis.nama : '-'));
                $("#detail-permohonan").html(": " + (res.data.permohonan || '-'));
                $("#detail-tanggal_lulus").html(": " + (res.data.tanggal_lulus || "-"));
                $("#detail-nomor_ijazah").html(": " + (res.data.nomor_ijazah || "-"));

                if (res.data.file_url) {
                    $("#detail-file")
                        .attr("href", res.data.file_url)
                        .removeClass("disabled")
                        .removeAttr("aria-disabled");
                } else {
                    $("#detail-file")
                        .attr("href", "#")
                        .addClass("disabled")
                        .attr("aria-disabled", "true");
                }

                $("#modalDetail").modal("show");
            } else {
                Swal.fire({
                    title: "Error!",
                    text: res.message,
                    icon: "error",
                });
            }
        },
        error: function (xhr, status, error) {
            var err = JSON.parse(xhr.responseText);
            Swal.fire({
                title: "Error!",
                text: err.message,
                icon: "error",
            });
        },
    });
});

$("#show_data").on("click", ".btn-delete", function () {
    Swal.fire({
        title: "Anda yakin membatalkan ajuan?",
        text: "Ajuan yang dibatalkan tidak dapat dipulihkan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Batalkan Ajuan!",
    }).then((result) => {
        if (result.isConfirmed) {
            let id = $(this).data("id");
            let url = window.Laravel.deleteData.replace(":id", id);

            $.ajax({
                url: url,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                beforeSend: function () {
                    Swal.fire({
                        title: "Mohon Tunggu",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: res.message,
                            icon: "success",
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: res.message,
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    var err = JSON.parse(xhr.responseText);
                    Swal.fire({
                        title: "Gagal!",
                        text: err.message,
                        icon: "error",
                    });
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        }
    });
});

$("#form-tambah").submit(function (e) {
    e.preventDefault();
    var form = this;
    // basic client-side validation to avoid sending empty payload
    var missing = [];

    // gather missing using form context
    ['permohonan','nomor_ijazah','tanggal_lulus'].forEach(function(name){
        var $el = $(form).find('[name="' + name + '"]');
        if (!$el.val() || $el.val().toString().trim() === '') missing.push(name);
    });

    if (missing.length) {
        Swal.fire({ title: 'Field required', text: 'Mohon isi: ' + missing.join(', '), icon: 'warning' });
        return;
    }

    Swal.fire({
        title: "Apakah inputan Anda sudah benar?",
        text: "Pastikan bahwa data yang Anda inputkan sudah benar",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Sudah!",
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData(form);

            $.ajax({
                url: $(form).attr("action"),
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: formData,
                beforeSend: function () {
                    Swal.fire({
                        title: "Mohon Tunggu",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function (res) {
                    if (res.status) {
                        $("#form-tambah input").val("");
                        $("#form-tambah textarea").val("");
                        $("#modalTambah").modal("hide");
                        Swal.fire({
                            title: "Berhasil!",
                            text: res.message || 'Ajuan terkirim',
                            icon: "success",
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: res.message || 'Terjadi kesalahan',
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    if (xhr.status === 422) {
                        var errors = {};
                        try { errors = JSON.parse(xhr.responseText).errors || {}; } catch(e){}
                        var msgs = Object.values(errors).map(function(v){ return v.join(' '); }).join('\n');
                        Swal.fire({ title: 'Validasi Gagal', html: msgs || 'Harap periksa inputan Anda', icon: 'warning' });
                    } else {
                        var err = {};
                        try { err = JSON.parse(xhr.responseText); } catch(e){}
                        Swal.fire({ title: 'Gagal!', text: err.message || 'Terjadi kesalahan', icon: 'error' });
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        }
    });
});

$("#form-edit").submit(function (e) {
    e.preventDefault();
    Swal.fire({
        title: "Apakah inputan Anda sudah benar?",
        text: "Pastikan bahwa data yang Anda inputkan sudah benar",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Sudah!",
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData(this);
            // console.log(formData);

            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: formData,
                beforeSend: function () {
                    Swal.fire({
                        title: "Mohon Tunggu",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function (res) {
                    Swal.close();
                    if (res.status) {
                        $("#form-edit input").val("");
                        $("#form-edit textarea#catatan-revisi").val("");
                        $("#modalEdit").modal("hide");
                        Swal.fire({
                            title: "Berhasil!",
                            text: res.message,
                            icon: "success",
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: res.message,
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
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
                        var err = {};
                        try {
                            err = JSON.parse(xhr.responseText);
                        } catch(e){}
                        Swal.fire({
                            title: "Gagal!",
                            text: err.message || 'Terjadi kesalahan',
                            icon: "error",
                        });
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        }
    });
});

})();
