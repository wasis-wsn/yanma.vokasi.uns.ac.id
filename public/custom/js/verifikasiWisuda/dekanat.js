const initializeDataTable = (status, year) => {
    return $("#wisuda-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_submit" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "no_seri_ijazah" },
            { data: "periode_wisuda" },
            { data: "action" },
            { data: "catatan" },
        ],
        columnDefs: [
            { className: "text-center", width: "3%", targets: [1] },
            { className: "text-wrap", targets: [3] },
            { className: "btn-group-vertical", targets: [7] },
        ],
        order: [[0, "desc"]],
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
                $("#detail-ijazah").html(": " + res.data.no_seri_ijazah);
                $("#detail-periode").html(": " + res.data.periode_wisuda);
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-kode").html(": " + res.data.kode_akses);
                // $(".btn-tolak").attr("data-id", id);
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