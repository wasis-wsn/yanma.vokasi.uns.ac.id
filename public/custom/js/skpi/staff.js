const initializeDataTable = (status, year) => {
    const columns = [];
    if (status == 2 || status == 3) {
        columns.push({ data: "id" });
    } else {
        columns.push({ data: "DT_RowIndex" });
    }
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

    if (status_table == 2 || status_table == 3) {
        $(".btn-selesai-many").removeClass("invisible");
    } else {
        $(".btn-selesai-many").addClass("invisible");
    }
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

$("#show_data").on("click", ".btn-update", function () {
    let id = $(this).data("id");
    let url = window.Laravel.getData.replace(":id", id);

    action = window.Laravel.routeProses.replace(":id", id);

    $.ajax({
        url: url,
        type: "GET",
        success: function (res) {
            if (res.status) {
                $("form#form-proses").attr("action", action);
                $('#form-no-surat').removeAttr('hidden');
                $("#no_surat").val(res.data.no_skpi);
                $("#status_id").val(res.data.status_id);

                $("#modalProses").modal("show");
            } else {
                Swal.fire({
                    title: "Gagal!",
                    text: res.message,
                    icon: "error",
                });
            }
        },
    });
});

$("#form-tambah").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let form = this;
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
                form.reset();
                $("#modalTambah").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });
                table.ajax.reload();
                refreshCount();
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

$("#form-proses").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let form = this;
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
                form.reset();
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

$(".btn-selesai-many").click(function (e) {
    e.preventDefault();
    let all_ids = [];
    $("input:checkbox[name='ids']:checked").each(function () {
        all_ids.push($(this).val());
    });

    let status = status_table + 1;

    $.ajax({
        url: window.Laravel.routeProsesMany,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            ids: all_ids,
            status: status,
        },
        dataType: "JSON",
        success: function (res) {
            if (res.status) {
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
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
    });
});
