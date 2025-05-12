const initializeDataTable = (status, year) => {
    return $("#legalisir-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_submit" },
            { data: "name" },
            { data: "nim" },
            { data: "prodi.name" },
            { data: "legalisir" },
            { data: "jumlah" },
            { data: "keperluan" },
            { data: "status_id" },
            { data: "action" },
            { data: "tanggal_ambil" },
            { data: "catatan" },
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
                targets: [10],
            },
            {
                className: "text-wrap",
                targets: [6],
            },
            {
                target: 0,
                visible: false,
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

let table = initializeDataTable(status_table, year);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    refreshCount();
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
    refreshCount();
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
                $("#detail-nama").text(": " + res.data.name);
                $("#detail-nim").text(": " + res.data.nim);
                $("#detail-prodi").text(": " + res.data.prodi.name);
                $("#detail-tahun_lulus").text(": " + res.data.tahun_lulus);
                $("#detail-no_wa").text(": " + res.data.no_wa);
                $("#detail-legalisir").text(": " + res.data.legalisir);
                $("#detail-jumlah").text(": " + res.data.jumlah);
                $("#detail-keperluan").text(": " + res.data.keperluan);
                $("#detail-catatan").text(": " + res.data.catatan);
                $("#detail-status").addClass(
                    `btn ${res.data.status.color} btn-sm`
                );
                $("#detail-status").text(res.data.status.name);

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