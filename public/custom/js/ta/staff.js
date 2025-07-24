const initializeDataTableTA = (status, year) => {
    return $("#ttdTA-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.TA.listData}?status=${status}&year=${year}`,
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
                className: "btn-group-vertical",
                targets: [5],
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

let tableTA = initializeDataTableTA(status_table_ta, year_ta);

$(".tahun-ta").click(function () {
    year_ta = $(this).data("year");
    $("#tahun_ta").html(year_ta);
    tableTA = initializeDataTableTA(status_table_ta, year_ta);
});

$(".status-ta").click(function () {
    status_table_ta = $(this).data("status");
    $("#status_ta").html($(this).html());
    tableTA = initializeDataTableTA(status_table_ta, year_ta);
});

setInterval(function () {
    tableTA.ajax.reload(null, false);
}, 300000);

// Handle detail button click for staff (view only)
$("#show_data_ta").on("click", ".btn-detail-ta", function () {
    let id = $(this).data("id");

    let url = window.Laravel.TA.routeShow.replace(":id", id);

    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (res.status) {
                $("#detail-ta-nama").html(": " + res.data.user.name);
                $("#detail-ta-nim").html(": " + res.data.user.nim);
                $("#detail-ta-status").html(res.data.status.name);
                $("#detail-ta-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
                $("#detail-ta-catatan").html(": " + res.data.catatan);
                $("#modalDetailTA").modal("show");
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
