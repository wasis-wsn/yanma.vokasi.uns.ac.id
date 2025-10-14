$(document).ready(function() {
    // Handle "Ya, Saya Setuju" button click - Direct confirmation
    $(document).on('click', '#btn-setuju', function() {
        const id = $(this).data('id');
        const button = $(this);

        Swal.fire({
            title: 'Konfirmasi Kehadiran Wisuda',
            text: 'Apakah Anda yakin bersedia mengikuti wisuda?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Bersedia',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesKonfirmasi(id, 'setuju', button);
            }
        });
    });

    // Handle "Tidak, Saya Tidak Setuju" button click - Direct confirmation
    $(document).on('click', '#btn-tidak-setuju', function() {
        const id = $(this).data('id');
        const button = $(this);

        Swal.fire({
            title: 'Konfirmasi Kehadiran Wisuda',
            text: 'Apakah Anda yakin tidak bersedia mengikuti wisuda?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tidak Bersedia',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesKonfirmasi(id, 'tidak_setuju', button);
            }
        });
    });

    // Function to process confirmation
    function prosesKonfirmasi(id, konfirmasi, button) {
        const url = window.Laravel.konfirmasi.replace(':id', id);
        const originalText = button.html();

        // Create FormData for the request
        const formData = new FormData();
        formData.append('konfirmasi', konfirmasi);

        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
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

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                button.prop('disabled', false).html(originalText);
            }
        });
    }
});
