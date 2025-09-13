const initializeDataTable = (status, year) => {
    return $("#wisuda-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listAdminFo}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_update" },
            { data: "tanggal_proses" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "no_seri_ijazah" },
            { data: "tanggal_terbit" },
            { data: "periode_wisuda" },
            { data: "status_id" },
            { data: "catatan" },
        ],
        columnDefs: [
            { className: "text-center", width: "3%", targets: [1] }, // DT_RowIndex
            { className: "text-center", width: "8%", targets: [2, 3] }, // tanggal_update, tanggal_proses
            { className: "text-wrap", width: "15%", targets: [4] }, // user.name
            { className: "text-center", width: "8%", targets: [5] }, // user.nim
            { className: "text-center", width: "10%", targets: [6, 7] }, // no_seri_ijazah, tanggal_terbit
            { className: "text-center", width: "10%", targets: [8] }, // periode_wisuda
            { className: "text-center align-middle", width: "8%", targets: [9] }, // status_id
            { className: "text-wrap", width: "15%", targets: [10] }, // catatan
        ],
        order: [[0, "desc"]],
    });
};

const initializeWisudawanDataTable = (year) => {
    return $("#wisudawan-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listWisudawan}?year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_update" },
            { data: "tanggal_proses" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "no_seri_ijazah" },
            { data: "tanggal_terbit" },
            { data: "periode_wisuda" },
            { data: "status_id" },
            { data: "catatan" },
        ],
        columnDefs: [
            { className: "text-center", width: "3%", targets: [1] }, // DT_RowIndex
            { className: "text-center", width: "8%", targets: [2, 3] }, // tanggal_update, tanggal_proses
            { className: "text-wrap", width: "15%", targets: [4] }, // user.name
            { className: "text-center", width: "8%", targets: [5] }, // user.nim
            { className: "text-center", width: "10%", targets: [6, 7] }, // no_seri_ijazah, tanggal_terbit
            { className: "text-center", width: "10%", targets: [8] }, // periode_wisuda
            { className: "text-center align-middle", width: "8%", targets: [9] }, // status_id
            { className: "text-wrap", width: "15%", targets: [10] }, // catatan
        ],
        order: [[0, "desc"]],
    });
};

let table = initializeDataTable(status_table, year);
let wisudawanTable;

// Initialize wisudawan table if the element exists and route is available
if ($("#wisudawan-datatable").length && window.Laravel.listWisudawan) {
    let year2 = $("#tahunWisudawanDropdown").length ? $("#tahunWisudawanDropdown").html() : year;
    wisudawanTable = initializeWisudawanDataTable(year2);
}

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTable(status_table, year);
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year);
});

// Add year dropdown handler for wisudawan table
$(".tahun-wisudawan-menu").click(function() {
    let year2 = $(this).data("year");
    $("#tahunWisudawanDropdown").html(year2);
    if (wisudawanTable && window.Laravel.listWisudawan) {
        wisudawanTable = initializeWisudawanDataTable(year2);
    }
});

// Auto refresh tables every 5 minutes
setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
    if (wisudawanTable) {
        wisudawanTable.ajax.reload(null, false);
    }
}, 300000);

// Detail modal functionality
$("#show_data").on("click", ".btn-detail", function () {
    let id = $(this).data("id");
    let url = window.Laravel.getData.replace(":id", id);

    $.ajax({
        url: url,
        type: "GET",
        success: function (res) {
            if (res.status) {
                $("#detail-nama").html(": " + res.data.user.name);
                $("#detail-nim").html(": " + res.data.user.nim);
                $("#detail-prodi").html(": " + res.data.user.prodis.name);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/verifWisuda/upload/${res.data.file}`
                );
                $("#detail-ijazah").html(": " + (res.data.no_seri_ijazah || 'Belum tersedia'));
                $("#detail-terbit").html(": " + (res.data.tanggal_terbit || 'Belum tersedia'));
                $("#detail-periode").html(": " + (res.data.periode_wisuda || 'Belum ditentukan'));
                $("#detail-catatan").html(": " + (res.data.catatan || 'Tidak ada catatan'));
                $("#detail-kode").html(": " + (res.data.kode_akses || 'Belum tersedia'));
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
