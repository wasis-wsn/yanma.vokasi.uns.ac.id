$(document).ready(function() {
    let year = $("#tahunDropdown").html();
    let status_table = $("#statusDropdown").data('status') || 'all';
    let prodi_table = $("#prodiDropdown").data('prodi') || 'all';

    const initializeDataTable = (status, year, prodi) => {
        return $("#suket-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listData}?status=${status}&year=${year}&prodi=${prodi}`,
            columns: [
                { data: "created_at", visible: false },
                { data: "DT_RowIndex" },
                { data: "status_id" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "user.prodis.name" },
                { data: "no_surat" },
                { data: "tanggal_ambil" },
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
            order: [[2, "asc"]],
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

    $(".prodi-menu").click(function () {
        prodi_table = $(this).data("prodi");
        $("#prodiDropdown").html($(this).html());
        table = initializeDataTable(status_table, year, prodi_table);
    });

    setInterval(function () {
        table.ajax.reload(null, false); // user paging is not reset on reload
    }, 300000);
});