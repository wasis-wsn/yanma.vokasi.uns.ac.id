/*
 *
 * Pengajuan TTD TA
 *
 */
const initializeDataTableTA = (status, year_ta) => {
    return $("#ttdTA-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.TA.listData}?status=${status}&year=${year_ta}`,
        columns: [
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "status_id" },
            { data: "created_at" },
            { data: "tanggal_ambil" },
            { data: "catatan" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [0],
            },
            {
                width: "5%",
                targets: [3],
            },
            {
                className: "text-wrap",
                targets: [1],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
    });
};

let table_ta = initializeDataTableTA(status_table_ta, year_ta);

$(".tahun-ta").click(function () {
    year_ta = $(this).data("year");
    $("#tahunDropdown").html(year_ta);
    table_ta = initializeDataTableTA(status_table_ta, year_ta);
});

$(".status-ta").click(function () {
    status_table_ta = $(this).data("status");
    $("#status_ta").html($(this).html());
    table_ta = initializeDataTableTA(status_table_ta, year);
});

setInterval(function () {
    table_ta.ajax.reload(null, false);
}, 300000);

/*
 *
 * Layanan SKL
 *
 */
const initializeDataTableSKL = (status, year, prodi) => {
    return $("#skl-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.skl.listData}?status=${status}&year=${year}&prodi=${prodi}`,
        columns: [
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "user.prodis.name" },
            { data: "tanggal_submit" },
            { data: "tanggal_proses" },
            { data: "status_id" },
            { data: "no_surat" },
            { data: "action" },
            { data: "tanggal_ambil" },
            { data: "catatan" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [0],
            },
            {
                width: "5%",
                targets: [3],
            },
            {
                className: "btn-group-vertical",
                targets: [8],
            },
            {
                className: "text-wrap",
                targets: [1, 3],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[0, "desc"]]
    });
};

let table = initializeDataTableSKL(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

$(".prodi-menu").click(function () {
    prodi_table = $(this).data("prodi"); 
    $("#prodiDropdown").html($(this).html());
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

$("#show_data_skl").on("click", ".btn-detail", function () {
    let id = $(this).data("id");
    let url = window.Laravel.skl.getData.replace(":id", id);

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
                $("#detail-lembar-persetujuan").attr(
                    "href",
                    `${window.Laravel.skl.baseUrl}/storage/skl/upload/${res.data.lembar_revisi}`
                );
                $("#detail-ss-bukti").attr(
                    "href",
                    `${window.Laravel.skl.baseUrl}/storage/skl/upload/${res.data.ss_ajuan_skl}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
                $("#modalDetailSKL").modal("show");
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

setInterval(function () {
    table.ajax.reload(null, false);
}, 300000);