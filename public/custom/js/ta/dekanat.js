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
    table_ta.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);
