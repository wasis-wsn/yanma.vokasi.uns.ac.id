const initializeDataTable = (status, year) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_submit" },
            { data: "tanggal_proses" },
            { data: "ormawa.name" },
            { data: "nama_kegiatan" },
            { data: "tempat" },
            { data: "mulai_kegiatan" },
            { data: "is_dana" },
            { data: "status_id" },
            { 
                data: "queue_number",
                className: "queue-info",
            },
            { data: "catatan" },
            { data: "id" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [1],
            },
            {
                className: "btn-group-vertical",
                targets: [11],
                orderable: false,
                searchable: false,
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[0, "desc"]],
    });
};

let table = initializeDataTable(status_table, year);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    refreshCount();
    table = initializeDataTable(status_table, year);
});

$('#btn-export').click(function () 
{
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year);
});

// Cek update antrian setiap 30 detik
setInterval(function() {
    if ($('#modalDetail').is(':visible') || $('.dataTables_filter input').is(':focus')) {
        $.ajax({
            url: '/sik/queue-status',
            type: "GET",
            success: function(res) {
                if (res.status) {
                    // Update tabel
                    table.ajax.reload(null, false);
                    
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
                $("#detail-pembina").html(": " + res.data.ormawa.pembina.name);
                $("#detail-ormawa").html(": " + res.data.ormawa.name);
                $("#detail-kegiatan").html(": " + res.data.nama_kegiatan);
                $("#detail-queue-number").text(res.data.queue_number);
                $.ajax({
                    url: '/sik/queue-status',
                    type: 'GET',
                    success: function(queueRes) {
                        if (queueRes.status) {
                            $("#detail-total-queue").text(queueRes.total_waiting);
                        }
                    }
                });
                $("#detail-ketua").html(
                    ": " +
                        res.data.ketua.nim +
                        " - " +
                        res.data.ketua.name +
                        " - " +
                        res.data.ketua.prodis.name
                );
                $("#detail-no-ketua").html(": " + res.data.ketua.no_wa);
                $("#detail-no-ormawa").html(": " + res.data.no_surat_ormawa);
                $("#detail-tanggal-ormawa").html(
                    ": " + res.data.tanggal_surat2
                );
                $("#detail-is-dana").html(res.data.is_dana == '1' ? ': Mengajukan Dana' : ': Tidak Mengajukan Dana');
                $("#detail-tanggal-lpj").html(": " + res.data.tanggal_lpj2);
                $("#detail-tanggal-kegiatan").html(
                    ": " + res.data.tanggal_kegiatan
                );
                $("#detail-tempat-kegiatan").html(": " + res.data.tempat);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/sik/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tanggal_proses);
                $("#detail-no").html(": " + res.data.no_surat);
                $(".btn-tolak").attr("data-id", id);
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
                const canProses = ["1", "3", "4", "5", "6"];
                if (canProses.includes(res.data.status_id)) {
                    $("#tombol-proses").data("id", id);
                    $("#tombol-proses").removeAttr("hidden");
                } else {
                    $("#tombol-proses").attr("hidden", true);
                }
                if (res.data.status_id == "6") {
                    $(".btn-tolak").attr("hidden", true);
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

    action = window.Laravel.routeProses.replace(":id", id);

    $("form#form-proses").attr("action", action);
    $("#form-proses textarea").val("");
    $("#form-proses input[type='text']").val("");
    $("#form-proses input[type='file']").val("");
    $.ajax({
        url: window.Laravel.getData.replace(":id", id),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (res.status) {
                $("#form-proses input[name='no_surat']").val(res.data.no_surat);
                $("#form-proses select[name='status_id']").val(res.data.status_id);
                const canChangeNoSurat = ["5","6"];
                if (canChangeNoSurat.includes(res.data.status_id)) {
                    $('#form-no-surat').removeAttr('hidden');
                } else {
                    $('#form-no-surat').attr('hidden', true);
                }
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

    $("#modalProses").modal("show");
};

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
    let form = this;
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
                $('#form-no-surat').attr('hidden', true);
                $('#form-surat-hasil').attr('hidden', true);
                form.reset();
                $("#modalProses").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });
                if (res.status_id && [5,6,7].includes(parseInt(res.status_id))) {
                    updateQueueNumbers();
                }
                table.ajax.reload();
                if (res.file) {
                    window.open(
                        window.Laravel.baseUrl + "/storage/surat_tugas/hasil/" + res.file,
                        "_blank"
                    );
                }
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
});

function updateQueueNumbers() {
    $.ajax({
        url: '/sik/update-queue',
        type: 'GET',
        success: function(res) {
            if (res.status) {
                table.ajax.reload(null, false);
                if ($('#modalDetail').is(':visible')) {
                    // Update juga di modal detail jika terbuka
                    $("#detail-queue-number").text(res.user_queue);
                    $("#detail-total-queue").text(res.total_waiting);
                }
            }
        }
    });
}
// $("#form-akhir").submit(function (e) {
//     e.preventDefault();
//     let formData = new FormData(this);
//     $.ajax({
//         url: $(this).attr("action"),
//         type: "POST",
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//         },
//         data: formData,
//         beforeSend: function () {
//             Swal.fire({
//                 title: "Mohon Tunggu",
//                 allowOutsideClick: false,
//                 didOpen: () => {
//                     Swal.showLoading();
//                 },
//             });
//         },
//         success: function (res) {
//             if (res.status) {
//                 $("#form-akhir input").val("");
//                 $("#form-akhir textarea").val("");
//                 $("#modalAkhir").modal("hide");
//                 Swal.fire({
//                     title: "Berhasil!",
//                     text: res.message,
//                     icon: "success",
//                     showConfirmButton: false,
//                     timer: 1500,
//                 });
//                 table.ajax.reload();
//                 refreshCount();
//             } else {
//                 Swal.fire({
//                     title: "Gagal!",
//                     text: res.message,
//                     icon: "error",
//                 });
//             }
//         },
//         error: function (xhr, status, error) {
//             var err = JSON.parse(xhr.responseText);
//             Swal.fire({
//                 title: "Gagal!",
//                 text: err.message,
//                 icon: "error",
//             });
//         },
//         cache: false,
//         contentType: false,
//         processData: false,
//     });
// });
