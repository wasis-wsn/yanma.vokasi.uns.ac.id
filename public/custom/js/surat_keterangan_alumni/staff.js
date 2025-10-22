(function() {
    'use strict';

    // Check if Laravel routes are properly defined
    if (!window.Laravel || !window.Laravel.listData) {
        console.error('Laravel routes are not properly defined');
        return;
    }

    // First, define all your functions at the top
    const initializeDataTableAlumni = (status, year) => {
        return $("#ska-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: `${window.Laravel.listData}?status=${status}&year=${year}`,
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
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "nama" },
            { data: "nim" },
            { data: "program_studi" },
            { data: "tanggal_lulus" },
            { data: "nomor_ijazah" },
            { data: "tanggal_submit" },
            { data: "file" },
            { data: "action" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [1],
            },
            {
                width: "10%",
                targets: [3,7],
            },
            {
                className: "btn-group-vertical",
                targets: [9],
            },
            {
                className: "text-wrap",
                targets: [2],
            },
            {
                className: "text-center",
                targets: [5,6,8],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[0, "desc"]],
    });
};

// Then initialize your variables and event handlers
let status_table = 'all';
let year = new Date().getFullYear();

// Initialize table only if element exists
let tableAlumni;

if ($("#ska-datatable").length) {
    tableAlumni = initializeDataTableAlumni(status_table, year);
}

$(document).on('click', '.tahun-menu', function () {
    year = $(this).data('year');
    $('#tahunDropdown').html(year);
    tableAlumni = initializeDataTableAlumni(status_table, year);
});

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

$(document).on('click', '.status-menu', function () {
    status_table = $(this).data('status');
    $('#statusDropdown').html($(this).html());
    tableAlumni = initializeDataTableAlumni(status_table, year);
});

// No queue polling — controller does not provide queue_number; polling removed.

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
                $("#detail-tanggal_lulus").html(": " + (res.data.tanggal_lulus || '-'));
                $("#detail-nomor_ijazah").html(": " + (res.data.nomor_ijazah || '-'));

                if (res.data.file) {
                    $("#detail-file").attr("href", '/storage/' + res.data.file).removeClass('disabled').removeAttr('aria-disabled');
                } else {
                    $("#detail-file").attr("href", '#').addClass('disabled').attr('aria-disabled', 'true');
                }

                $("#detail-no").html(res.data.no_surat ? ': ' + res.data.no_surat : ': -');
                $("#detail-catatan").html(res.data.catatan ? ': ' + res.data.catatan : ': -');
                $("#detail-proses").html(res.data.tanggal_proses ? ': ' + res.data.tanggal_proses : ': -');
                if (res.data.status) {
                    $("#detail-status").html(res.data.status.name);
                    $("#detail-status").attr("class", `btn ${res.data.status.color} btn-small`);
                }

                const canProses = ["1", "3", "4", "5", "6"];
                if (canProses.includes(res.data.status_id)) {
                    $("#tombol-proses").data("id", id);
                    $("#tombol-proses").html('<i class="fa fa-file-pen"></i> Proses');
                    $("#tombol-proses").removeAttr("hidden");
                } else if (res.data.status_id == '9') {
                    // When status is 9, show Edit button so staff can edit the status
                    $("#tombol-proses").data("id", id);
                    $("#tombol-proses").html('<i class="fa fa-edit"></i> Edit');
                    $("#tombol-proses").removeAttr("hidden");
                } else {
                    $("#tombol-proses").attr("hidden", true);
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

$("#show_data").on("click", ".btn-proses", function () {
    showModalProses(this);
});

$("#show_data").on("click", ".btn-edit", function () {
    showModalEdit(this);
});

function showModalEdit(p) {
    let id = $(p).data("id");
    let action = window.Laravel.revisi.replace(":id", id);
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
                $("#form-edit input[name='tanggal_lulus']").val(res.data.tanggal_lulus || '');
                $("#form-edit input[name='nomor_ijazah']").val(res.data.nomor_ijazah || '');
                $("#form-edit input[type='file']").val('');
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
}

$("#tombol-proses").on("click", function () {
    $("#modalDetail").modal("hide");
    showModalProses(this);
});

function showModalProses(p) {
    let id = $(p).data("id");

    action = window.Laravel.routeProses ? window.Laravel.routeProses.replace(":id", id) : '';

    $("form#form-proses").attr("action", action);
    $("#form-proses textarea").val("");
    $("#form-proses input[type='text']").val("");
    $("#form-proses input[type='file']").val("");
    if (window.Laravel.getData) {
        $.ajax({
            url: window.Laravel.getData.replace(":id", id),
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                $("#form-proses input[name='no_surat']").val(res.data.no_surat || '');
                $("#form-proses select[name='status_id']").val(res.data.status_id || '');
                const canChangeNoSurat = ["5","6"];
                if (canChangeNoSurat.includes(res.data.status_id)) {
                    $('#form-no-surat').removeAttr('hidden');
                } else {
                    $('#form-no-surat').attr('hidden', true);
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
    }

    $("#modalProses").modal("show");
}

$('#status_id').change(function () {
    const canChangeNoSurat = ['5','6'];
    if (canChangeNoSurat.includes($(this).val())) {
        $('#form-no-surat').removeAttr('hidden');
    } else {
        $('#form-no-surat').attr('hidden', true);
    }
    if ($(this).val() === '9') {
        $('#form-surat-hasil').removeAttr('hidden');
    } else {
        $('#form-surat-hasil').attr('hidden', true);
    }
});

$("#form-proses").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                $("#form-proses input").val("");
                $("#form-proses textarea").val("");
                $("#modalProses").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
                tableAlumni.ajax.reload();
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
});

$("#form-edit").submit(function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Apakah inputan Anda sudah benar?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Sudah!"
    }).then(function(result){
        if (!result.isConfirmed) return;

        let formData = new FormData(e.target);
        formData.append('_method', 'PUT');

        $.ajax({
            url: $(e.target).attr("action"),
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: formData,
            processData: false,
            contentType: false,
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
                    $("#form-edit textarea").val("");
                    $("#modalEdit").modal("hide");
                    Swal.fire({
                        title: "Berhasil!",
                        text: res.message,
                        icon: "success",
                    });
                    tableAlumni.ajax.reload();
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
                    var errors = {};
                    try { errors = JSON.parse(xhr.responseText).errors || {}; } catch(e){}
                    var msgs = Object.values(errors).map(function(v){ return v.join(' '); }).join('\n');
                    Swal.fire({
                        title: 'Validasi Gagal',
                        html: msgs || 'Harap periksa inputan Anda',
                        icon: 'warning'
                    });
                } else {
                    var msg = 'Terjadi kesalahan';
                    try { var e = JSON.parse(xhr.responseText); if (e.message) msg = e.message; } catch(e){}
                    Swal.fire({
                        title: 'Gagal!',
                        text: msg,
                        icon: 'error'
                    });
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
});

})();
