const initializeDataTable = (status_table, year, prodi_table) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: window.Laravel.listData,
            type: 'GET',
            data: function(d) {
                // Add custom parameters
                return $.extend({}, d, {
                    status: status_table,
                    year: year,
                    prodi: prodi_table
                });
            }
        },
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "nama_prodi" },
            { data: "tanggal_submit" },
            { data: "tanggal_proses" },
            { data: "nama_kegiatan" },
            { data: "mulai_kegiatan" },
            { data: "no_surat" },
            { data: "status_id" },
            // { 
            //     data: "queue_number",
            //     className: "queue-info"
            // },
            { data: "catatan" },
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

let table = initializeDataTable(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTable(status_table, year, prodi_table);
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year, prodi_table);
});

$(".prodi-menu").on("click", function (e) {
    e.preventDefault();

    var selectedProdi = $(this).data("status"); // Ambil data-status

    if (!selectedProdi) {
        selectedProdi = "all"; // Default ke 'all' jika undefined
    }

    $("#prodiDropdown").text($(this).text()); // Ubah teks tombol dropdown
    $("#prodiDropdown").data("status", selectedProdi); // Perbarui data-status

    // Pastikan DataTable diperbarui setelah perubahan filter
    table.destroy();
    table = initializeDataTable(status_table, year, selectedProdi);
});

// setInterval(function() {
//     if ($('#modalDetail').is(':visible')) {
//         $.ajax({
//             url: window.Laravel.queueStatus,
//             type: "GET",
//             success: function(res) {
//                 if (res.status) {
//                     $("#detail-queue-number").text(res.user_queue);
//                     $("#detail-total-queue").text(res.total_waiting);
                    
//                     // Update juga di tabel
//                     table.ajax.reload(null, false);
//                 }
//             }
//         });
//     }
// }, 30000); // 30 detik

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

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
                // $("#detail-antrian").html(": " + res.data.queue_number);
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
                $("#detail-no").html(res.data.no_surat ? res.data.no_surat : ':');
                $("#detail-catatan").html(res.data.catatan ? res.data.catatan : ':');
                $("#detail-proses").html(res.data.tanggal_proses ? res.data.tanggal_proses : ':');
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
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