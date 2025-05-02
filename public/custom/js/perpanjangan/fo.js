const initializeDataTable = (status_table, year, prodi_table) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: window.Laravel.listData,
            type: 'GET',
            data: function(d) {
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
            { data: "status_id" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "user.prodis.name" },
            { data: "tanggal_ambil" },
            // { 
            //     data: "queue_number",
            //     className: "queue-info"
            // },
            { data: "action" },
            { data: "catatan" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [1],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[2, "asc"]],
    });
};

// Deklarasi variabel di luar fungsi
var year = $("#tahunDropdown").html();
var status_table = $("#statusDropdown").data('status');
var prodi_table = $("#prodiDropdown").data('prodi') || "all";

let table = initializeDataTable(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table.destroy();
    table = initializeDataTable(status_table, year, prodi_table);
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table.destroy();
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

$("#show_data").on("click", ".btn-proses", function () {
    let id = $(this).data("id");
    action = window.Laravel.update.replace(":id", id);
    $("form#form-proses").attr("action", action);
    $("#form-proses textarea").val("");
    // $("#detail-antrian").html(": " + res.data.queue_number);
    $("#modalProses").modal("show");
});

$("#form-proses").submit(function (e) {
    e.preventDefault();
    let form = this;
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