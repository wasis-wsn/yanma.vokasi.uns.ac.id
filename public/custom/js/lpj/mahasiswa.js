var table = $("#suket-datatable").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    ajax: window.Laravel.listData,
    columns: [
        { data: "DT_RowIndex" },
        { data: "no_surat" },
        { data: "nama_kegiatan" },
        { data: "status_id" },
        { data: "catatan" },
        { data: "id" },
    ],
    columnDefs: [
        {
            className: "text-center",
            targets: [0],
        },
        {
            targets: [5],
            className: "btn-group-vertical",
            orderable: false,
            searchable: false,
        },
    ],
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

$(".selesai_kegiatan").change(function () {
    let selesai = $(this).val();
    let date = new Date(selesai);
    date.setDate(date.getDate() + 14);
    let lpj = date.toISOString().slice(0, 10);
    console.log(lpj);
    $(".tanggal_lpj").val(lpj);
});

$("#show_data").on("click", ".btn-upload", function () {
    let id = $(this).data("id");
    let action = window.Laravel.upload.replace(":id", id);
    $("#form-upload").attr('action', action);
    $('#modalUpload').modal('show');
});

$("#form-upload").submit(function (e) {
    e.preventDefault();
    let form = this;
    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                "content"
            ),
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
                form.reset()
                $("#modalUpload").modal("hide");
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
        cache: false,
        contentType: false,
        processData: false,
    });
});