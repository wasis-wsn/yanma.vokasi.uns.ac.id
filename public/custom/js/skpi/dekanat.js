const initializeDataTable = (status, year) => {
    const columns = [];
    columns.push({ data: "DT_RowIndex" });
    columns.push(
        { data: "no_skpi" },
        { data: "user.nim" },
        { data: "user.name" },
        { data: "periode_wisuda" },
        { data: "status_id" },
        { data: "action" },
        { data: "tanggal_ambil" },
        { data: "catatan" }
    );
    return $("#skpi-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: columns,
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [0],
            },
            {
                className: "btn-group-vertical",
                targets: [6],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
    });
};

let table = initializeDataTable(status_table, year);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
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