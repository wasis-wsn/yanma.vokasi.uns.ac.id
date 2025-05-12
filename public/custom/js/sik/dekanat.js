const initializeDataTable = (status, year) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "DT_RowIndex" },
            { data: "created_at" },
            { data: "tanggal_proses" },
            { data: "ormawa.name" },
            { data: "nama_kegiatan" },
            { data: "tempat" },
            { data: "mulai_kegiatan" },
            { data: "is_dana" },
            { data: "status_id" },
            { data: "catatan" },
            { data: "id" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [0],
            },
            {
                className: "btn-group-vertical",
                targets: [10],
                orderable: false,
                searchable: false,
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
    refreshCount();
    table = initializeDataTable(status_table, year);
});

$('#btn-export').click(function () 
{
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
                $("#detail-pembina").html(": " + res.data.ormawa.pembina.name);
                $("#detail-ormawa").html(": " + res.data.ormawa.name);
                $("#detail-kegiatan").html(": " + res.data.nama_kegiatan);
                $("#detail-ketua").html(
                    ": " +
                        res.data.ketua.nim +
                        " - " +
                        res.data.ketua.name +
                        " - " +
                        res.data.ketua.prodis.name
                );
                $("#detail-no-ketua").html(": " + res.data.ketua.no_wa);
                $("#detail-no-ormawa").html(": " + res.data.no_surat_ormawa);
                $("#detail-tanggal-ormawa").html(
                    ": " + res.data.tanggal_surat2
                );
                $("#detail-is-dana").html(res.data.is_dana == '1' ? ': Mengajukan Dana' : ': Tidak Mengajukan Dana');
                $("#detail-tanggal-lpj").html(": " + res.data.tanggal_lpj2);
                $("#detail-tanggal-kegiatan").html(
                    ": " + res.data.tanggal_kegiatan
                );
                $("#detail-tempat-kegiatan").html(": " + res.data.tempat);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/sik/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tanggal_proses);
                $("#detail-no").html(": " + res.data.no_surat);
                $(".btn-tolak").attr("data-id", id);
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