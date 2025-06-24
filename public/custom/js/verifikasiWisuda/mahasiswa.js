$('#btn-setuju').click(function() {
    let id = $(this).data('id');
    let action = window.Laravel.konfirmasi.replace(':id', id);
    
    $('#form-konfirmasi').attr('action', action);
    $('#modalKonfirmasiLabel').text('Konfirmasi Keikutsertaan Wisuda');
    $('#konfirmasi-text').text('Apakah Anda yakin bersedia mengikuti wisuda pada periode ini?');
    $('#konfirmasi_value').val('setuju');
    $('#btn-konfirmasi-submit').removeClass('btn-danger').addClass('btn-success').text('Ya, Saya Setuju');
    $('#modalKonfirmasi').modal('show');
});

$('#btn-tidak-setuju').click(function() {
    let id = $(this).data('id');
    let action = window.Laravel.konfirmasi.replace(':id', id);
    
    $('#form-konfirmasi').attr('action', action);
    $('#modalKonfirmasiLabel').text('Konfirmasi Penolakan Wisuda');
    $('#konfirmasi-text').text('Apakah Anda yakin tidak bersedia mengikuti wisuda pada periode ini?');
    $('#konfirmasi_value').val('tidak_setuju');
    $('#btn-konfirmasi-submit').removeClass('btn-success').addClass('btn-danger').text('Ya, Saya Tidak Setuju');
    $('#modalKonfirmasi').modal('show');
});

$("#form-konfirmasi").submit(function (e) {
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
                text: "Sedang memproses konfirmasi...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        success: function (res) {
            if (res.status) {
                $("#form-konfirmasi")[0].reset();
                $("#modalKonfirmasi").modal("hide");
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
