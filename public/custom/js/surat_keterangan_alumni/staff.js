const initializeDataTableAlumni = (status, year) => {
    return $("#ska-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "tanggal_submit" },
            { data: "tanggal_proses" },
            { data: "keperluan" },
            { data: "no_surat" },
            { data: "status_id" },
            {
                data: "queue_number",
                className: "queue-info",
            },
            { data: "catatan" },
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
                targets: [4,5],
            },
            {
                className: "btn-group-vertical",
                targets: [10],
            },
            {
                className: "text-wrap",
                targets: [2],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[0, "desc"]],
    });
};

let tableAlumni = initializeDataTableAlumni(typeof status_table !== 'undefined' ? status_table : '', typeof year !== 'undefined' ? year : '');

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

// Cek update antrian setiap 30 detik
setInterval(function() {
    if ($('#modalDetail').is(':visible') || $('.dataTables_filter input').is(':focus')) {
        $.ajax({
            url: '/suket/queue-status',
            type: "GET",
            success: function(res) {
                if (res.status) {
                    // Update tabel
                    tableAlumni.ajax.reload(null, false);

                    // Update modal detail jika terbuka
                    if ($('#modalDetail').is(':visible')) {
                        $("#detail-queue-number").text(res.user_queue);
                        $("#detail-total-queue").text(res.total_waiting);
                    }
                }
            }
        });
    }
}, 30000);

$("#show_data").on("click", ".btn-detail", function () {
    let id = $(this).data("id");

    let url = window.Laravel.getData.replace(":id", id);

    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (res.status) {
                $("#detail-nama").html(": " + res.data.nama);
                $("#detail-nim").html(": " + res.data.nim);
                $("#detail-prodi").html(": " + res.data.program_studi);
                $("#detail-tahun_akademik").html(": " + res.data.tanggal_lulus);
                $("#detail-keperluan").html(": " + (res.data.keperluan || ''));
                // Use download route if available
                $("#detail-file").attr("href", window.Laravel.download.replace(":id", id));
                $("#detail-catatan").html(": " + (res.data.catatan || ''));
                $("#detail-status").html(res.data.status ? res.data.status.name : '');
                if (res.data.status_id == "1" || res.data.status_id == "4") {
                    $("#detail-status").attr("class", "btn btn-light btn-small");
                } else if (res.data.status_id == "2") {
                    $("#detail-status").attr("class", "btn btn-primary btn-small");
                } else if (res.data.status_id == "3") {
                    $("#detail-status").attr("class", "btn btn-warning btn-small");
                } else if (res.data.status_id == "5") {
                    $("#detail-status").attr("class", "btn btn-success btn-small");
                } else if (res.data.status_id == "6") {
                    $("#detail-status").attr("class", "btn btn-danger btn-small");
                }
                $("#detail-queue-number").text(res.data.queue_number);
                $.ajax({
                    url: '/suket/queue-status',
                    type: 'GET',
                    success: function(queueRes) {
                        if (queueRes.status) {
                            $("#detail-total-queue").text(queueRes.total_waiting);
                        }
                    }
                });
                const canProses = ["1", "3", "4", "5", "6"];
                if (canProses.includes(res.data.status_id)) {
                    $("#tombol-proses").data("id", id);
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
            type: "POST",
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
                if (res.status_id && [5,6,7].includes(parseInt(res.status_id))) {
                    updateQueueNumbersAlumni();
                }
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

function updateQueueNumbersAlumni() {
    $.ajax({
        url: '/suket/update-queue',
        type: 'GET',
        success: function(res) {
            if (res.status) {
                tableAlumni.ajax.reload(null, false);
                if ($('#modalDetail').is(':visible')) {
                    $("#detail-queue-number").text(res.user_queue);
                    $("#detail-total-queue").text(res.total_waiting);
                }
            }
        }
    });
}
