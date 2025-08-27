const initializeDataTableSKL = (status, year, prodi) => {
    return $("#skl-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.skl.listData}?status=${status}&year=${year}&prodi=${prodi}`,
        columns: [
            { data: "created_at", visible: false },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // Use the ID that was already encoded by the server
                    return '<input type="checkbox" class="form-check-input row-checkbox" value="' + row.id + '">';
                }
            },
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "user.prodis.name" },
            { data: "created_at" },
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

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

$("#show_data_skl").on("click", ".btn-proses", function () {
    let id = $(this).data("id");

    let action = window.Laravel.skl.routeProses.replace(":id", id);

    $("#form-proses").attr("action", action);

    $("#modalProses").modal("show");
});

$("#form-proses").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        beforeSend: function () {
            Swal.fire({
                title: "Mohon Tunggu",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        success: function (res) {
            if (res.status) {
                $("#modalProses").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });
                table.ajax.reload();
            } else {
                Swal.fire({
                    title: "Gagal!",
                    text: res.message,
                    icon: "error",
                });
            }
        },
        error: function (xhr, status, error) {
            var err = JSON.parse(xhr.responseText);
            Swal.fire({
                title: "Gagal!",
                text: err.message,
                icon: "error",
            });
        },
        cache: false,
        contentType: false,
        processData: false,
    });
});
