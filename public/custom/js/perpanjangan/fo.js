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
                { data: "status.name" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "user.prodis.name" },
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
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, 'All']
            ],
            order: [[2, "asc"]],
        });
    };

    let table = initializeDataTable(status_table, year, prodi_table);

    $(".tahun-menu").click(function () {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table.destroy();
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $(".status-menu").click(function () {
        status_table = $(this).data("status");
        $("#statusDropdown").html($(this).html());
        table.destroy();
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $(".prodi-menu").on("click", function (e) {
        e.preventDefault();

        var selectedProdi = $(this).data("status");

        if (!selectedProdi) {
            selectedProdi = "all";
        }

        $("#prodiDropdown").text($(this).text());
        $("#prodiDropdown").data("status", selectedProdi);

        table.destroy();
        table = initializeDataTable(status_table, year, selectedProdi);
    });

    setInterval(function () {
        table.ajax.reload(null, false); // user paging is not reset on reload
    }, 300000);

    $("#show_data").on("click", ".btn-proses", function () {
        let id = $(this).data("id");

        action = window.Laravel.update.replace(":id", id);

        $("form#form-proses").attr("action", action);
        $("#form-proses textarea").val("");
        $("#modalProses").modal("show");
    });

    $("#form-proses").submit(function (e) {
        e.preventDefault();
        let form = this;
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
});
