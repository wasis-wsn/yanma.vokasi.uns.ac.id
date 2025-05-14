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
                { data: "tanggal_submit" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "nama_prodi" },
                { data: "no_surat" },
                { data: "status_id" },
                { data: "catatan" },
                { data: "action" }
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
                    targets: [9],
                },
                {
                    className: "text-wrap",
                    targets: [3],
                },
            ],
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, 'All']
            ],
            order: [[0, "desc"]],
        });
    };

    let table = initializeDataTable(status_table, year, prodi_table);

    $(".tahun-menu").click(function () {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $('#btn-export').click(function () {
        $('#form-export').attr('action', window.Laravel.export);
        $('#modalExport').modal('show');
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

    $("#show_data").on("click", ".btn-detail", function () {
        let id = $(this).data("id");

        let url = window.Laravel.getData.replace(":id", id);

        $("#detail-izin-cuti").empty();

        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                if (res.status) {
                    $("#detail-nama").html(": " + res.data.user.name);
                    $("#detail-nim").html(": " + res.data.user.nim);
                    $("#detail-prodi").html(": " + res.data.user.prodis.name);
                    $("#detail-tahun-akademik").html(": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester);
                    $("#detail-file").attr(
                        "href",
                        `${window.Laravel.baseUrl}/storage/undur/upload/${res.data.file}`
                    );
                    $("#detail-catatan").html(
                        res.data.catatan ? ": " + res.data.catatan : ":"
                    );
                    $("#detail-proses").html(": " + res.data.tgl_proses);
                    $("#detail-no").html(
                        res.data.no_surat ? ": " + res.data.no_surat : ":"
                    );
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
});