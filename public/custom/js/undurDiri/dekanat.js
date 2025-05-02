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
            { data: "tanggal_submit" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "nama_prodi" },
            { data: "no_surat" },
            { data: "status_id" },
            { 
                data: "queue_number",
                className: "queue-info"
            },
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
                targets: [9],
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

// Cek update antrian setiap 30 detik
setInterval(function() {
    if ($('#modalDetail').is(':visible') || $('.dataTables_filter input').is(':focus')) {
        $.ajax({
            url: '/undurDiri/queue-status',
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

    $("#detail-izin-cuti").empty();

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
                $("#detail-antrian").html(": " + res.data.queue_number);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/undur/upload/${res.data.file}`
                );
                $("#detail-catatan").html(
                    res.data.catatan ? ": " + res.data.catatan : ":"
                );
                $("#detail-proses").html(": " + res.data.tgl_proses);
                $("#detail-no").html(
                    res.data.no_surat ? ": " + res.data.no_surat : ":"
                );
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

function updateQueueNumbers() {
    $.ajax({
        url: '/undurDiri/update-queue',
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