$('#btn-tambah').click(function() {
    let action = window.Laravel.store;

    $('#form-tambah').attr('action', action);
    $('#titleModalTambah').html('Tambah Ajuan Verifikasi Wisuda');
    $('#form-tambah input[type="file"]').val('');
    $('#modalTambah').modal('show');
})

$("#btn-edit").click(function () {
    let id = $(this).data("id");
    let action = window.Laravel.editData.replace(":id", id);

    $('#form-tambah').attr('action', action);
    $('#titleModalTambah').html('Edit Ajuan Verifikasi Wisuda');
    $('#form-tambah input[type="file"]').val('');
    $('#modalTambah').modal('show');
});

$("#form-tambah").submit(function (e) {
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
                text: "Semakin besar ukuran file, semakin banyak waktu yang diperlukan.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        success: function (res) {
            if (res.status) {
                $("#form-tambah input").val("");
                $("#modalTambah").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                });
                window.location.reload();
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
