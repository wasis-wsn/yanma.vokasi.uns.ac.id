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
                { data: "tanggal_ambil" },
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
            error: function (xhr, error, thrown) {
                console.log('DataTables error:', error);
            }
        });
    };

    let table = initializeDataTable(status_table, year, prodi_table);

    // Event handlers for filters
    $(".tahun-menu").click(function () {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table.destroy();
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $('#btn-export').click(function () {
        $('#form-export').attr('action', window.Laravel.export);
        $('#modalExport').modal('show');
    });

    $(".status-menu").click(function () {
        status_table = $(this).data("status");
        $("#statusDropdown").html($(this).html());
        table.destroy();
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $(".prodi-menu").on("click", function (e) {
        e.preventDefault();
    
        let selectedProdi = $(this).data("status");
    
        if (!selectedProdi) {
            selectedProdi = "all";
        }
    
        $("#prodiDropdown").text($(this).text());
        $("#prodiDropdown").data("status", selectedProdi);
    
        table.destroy();
        table = initializeDataTable(status_table, year, selectedProdi);
    });

    setInterval(function () {
        table.ajax.reload(null, false);
    }, 300000);

    $("#show_data").on("click", ".btn-proses", function () {
        let id = $(this).data("id");
        action = window.Laravel.update.replace(":id", id);
        $("form#form-proses").attr("action", action);
        $("#form-proses textarea").val("");
    });
});

