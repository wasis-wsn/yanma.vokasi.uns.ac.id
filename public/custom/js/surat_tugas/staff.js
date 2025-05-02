const initializeDataTable = (status, year) => {
    return $("#suket-datatable").DataTable({
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
            { data: "nama_kegiatan" },
            { data: "mulai_kegiatan" },
            { data: "no_surat" },
            { data: "status_id" },
            // { 
            //     data: "queue_number",
            //     className: "queue-info",
            // },
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
                className: "text-wrap",
                targets: [2],
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

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year);
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

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
                $("#detail-kegiatan").html(": " + res.data.nama_kegiatan);
                $("#detail-nama").html(": " + res.data.user.name);
                $("#detail-nim").html(": " + res.data.user.nim);
                $("#detail-prodi").html(": " + res.data.user.prodis.name);
                // $("#detail-queue-number").text(res.data.queue_number);
                // $.ajax({
                //     url: '/st/queue-status',
                //     type: 'GET',
                //     success: function(queueRes) {
                //         if (queueRes.status) {
                //             $("#detail-total-queue").text(queueRes.total_waiting);
                //         }
                //     }
                // });
                $("#detail-no-wa").html(": " + res.data.user.no_wa);
                $("#detail-tanggal-kegiatan").html(
                    ": " + res.data.tanggal_kegiatan
                );
                $("#detail-penyelenggara").html(": " + res.data.penyelenggara);
                $("#detail-tempat-kegiatan").html(": " + res.data.tempat);
                $("#detail-delegasi").html(": " + res.data.delegasi);
                $("#detail-peserta").html(": " + res.data.jumlah_peserta);
                $("#detail-dospem").html(": " + res.data.dospem);
                $("#detail-nip-dospem").html(": " + res.data.nip_dospem);
                $("#detail-nidn-dospem").html(": " + res.data.nidn_dospem);
                $("#detail-unit-dospem").html(": " + res.data.unit_dospem);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/surat_tugas/upload/${res.data.file}`
                );
                $("#detail-no").html(res.data.no_surat ? ': ' + res.data.no_surat : ':');
                $("#detail-catatan").html(res.data.catatan ? ': ' + res.data.catatan : ':');
                $("#detail-proses").html(res.data.tanggal_proses ? ': ' + res.data.tanggal_proses : ':');
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
                // if (res.status_id && [5,6,7].includes(parseInt(res.status_id))) {
                //     updateQueueNumbers();
                // }
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
});

// function updateQueueNumbers() {
//     $.ajax({
//         url: '/perpanjangan/update-queue',
//         type: 'GET',
//         success: function(res) {
//             if (res.status) {
//                 table.ajax.reload(null, false);
//                 if ($('#modalDetail').is(':visible')) {
//                     // Update juga di modal detail jika terbuka
//                     $("#detail-queue-number").text(res.user_queue);
//                     $("#detail-total-queue").text(res.total_waiting);
//                 }
//             }
//         }
//     });
// }