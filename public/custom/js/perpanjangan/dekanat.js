const initializeDataTable = (status_table, year, prodi_table) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: window.Laravel.listData,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                // Add our custom parameters
                d.status = status_table || 'all';
                d.year = year || new Date().getFullYear();
                d.prodi = prodi_table || 'all';
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', xhr.responseText);
                let errorMsg = 'Failed to load data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            }
        },
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_submit" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "nama_prodi" },
            { data: "no_surat" },
            { data: "tahun_akademik" },
            { data: "perpanjangan_ke" },
            { data: "status.name" },
            // { 
            //     data: "queue_number",
            //     className: "queue-info"
            // },
            { data: "catatan" },
            { data: "action" }
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [1],
            },
            {
                width: "10%",
                targets: [4],
            },
            {
                className: "btn-group-vertical",
                targets: [11],
            },
            {
                className: "text-wrap",
                targets: [3],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[0, "desc"]],
    });
};

var year = $("#tahunDropdown").html();
var status_table = $("#statusDropdown").data('status');
var prodi_table = $("#prodiDropdown").data('prodi') || "all";

let table = initializeDataTable(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTable(status_table, year, prodi_table);
});

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
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
                $("#detail-nama").html(": " + res.data.user.name);
                $("#detail-nim").html(": " + res.data.user.nim);
                $("#detail-prodi").html(": " + res.data.user.prodis.name);
                $("#detail-tahun-akademik").html(": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester);
                // $("#detail-antrian").html(": " + res.data.queue_number);
                $("#detail-perpanjangan").html(": " + res.data.perpanjangan_ke);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/perpanjangan/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tgl_proses);
                $("#detail-no").html(": " + res.data.no_surat);
                $(".btn-tolak").attr("data-id", id);
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

// function updateQueueNumbers() {
//     $.ajax({
//         url: window.Laravel.updateQueue, // Menggunakan route dari object Laravel
//         type: 'GET',
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function(res) {
//             if (res.status) {
//                 table.ajax.reload(null, false);
//                 if ($('#modalDetail').is(':visible')) {
//                     $("#detail-queue-number").text(res.current_queue);
//                     $("#detail-total-queue").text(res.total_waiting);
//                 }
//             }
//         },
//         error: function(xhr) {
//             console.error('Error updating queue:', xhr.responseText);
//             toastr.error('Gagal memperbarui antrian');
//         }
//     });
// }