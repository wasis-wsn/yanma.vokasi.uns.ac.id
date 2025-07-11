$(document).ready(function() {
    // Handle "Ya, Saya Setuju" button click
    $(document).on('click', '#btn-setuju', function() {
        const id = $(this).data('id');
        $('#form-konfirmasi-setuju input[name="verifikasi_id"]').val(id);
        $('#modalKonfirmasiSetuju').modal('show');
    });

    // Handle "Tidak, Saya Tidak Setuju" button click
    $(document).on('click', '#btn-tidak-setuju', function() {
        const id = $(this).data('id');
        $('#form-konfirmasi-tolak input[name="verifikasi_id"]').val(id);
        $('#modalKonfirmasiTolak').modal('show');
    });

    // Handle form submit for "Setuju" with file upload
    $('#form-konfirmasi-setuju').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#form-konfirmasi-setuju input[name="verifikasi_id"]').val();
        const url = window.Laravel.konfirmasi.replace(':id', id);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#form-konfirmasi-setuju button[type="submit"]').prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Mengupload...');
            },
            success: function(response) {
                if (response.status) {
                    $('#modalKonfirmasiSetuju').modal('hide');
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
                toastr.error(errorMessage);
            },
            complete: function() {
                $('#form-konfirmasi-setuju button[type="submit"]').prop('disabled', false)
                    .html('<i class="fa fa-upload"></i> Upload & Konfirmasi Setuju');
            }
        });
    });

    // Handle form submit for "Tidak Setuju" with file upload
    $('#form-konfirmasi-tolak').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#form-konfirmasi-tolak input[name="verifikasi_id"]').val();
        const url = window.Laravel.konfirmasi.replace(':id', id);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#form-konfirmasi-tolak button[type="submit"]').prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Mengupload...');
            },
            success: function(response) {
                if (response.status) {
                    $('#modalKonfirmasiTolak').modal('hide');
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
                toastr.error(errorMessage);
            },
            complete: function() {
                $('#form-konfirmasi-tolak button[type="submit"]').prop('disabled', false)
                    .html('<i class="fa fa-upload"></i> Upload & Konfirmasi Tidak Bersedia');
            }
        });
    });
});
