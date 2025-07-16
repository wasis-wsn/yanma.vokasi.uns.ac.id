$(document).ready(function() {
    // File upload form handler
    $(document).on('submit', '#form-upload-validasi', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Validate file
        var fileInput = $('#file_validasi')[0];
        var file = fileInput.files[0];
        
        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Silakan pilih file terlebih dahulu!'
            });
            return;
        }
        
        // Check file size (100MB = 104857600 bytes)
        if (file.size > 104857600) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ukuran file terlalu besar! Maksimal 100MB.'
            });
            return;
        }
        
        // Check file type
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Format file tidak diizinkan! Gunakan file PDF.'
            });
            return;
        }
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        
        $.ajax({
            url: window.Laravel.uploadValidasi,
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
                        timer: 1500
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
                var response = xhr.responseJSON;
                var message = 'Terjadi kesalahan saat upload file.';
                
                if (response && response.message) {
                    message = response.message;
                } else if (response && response.errors) {
                    var errors = Object.values(response.errors).flat();
                    message = errors.join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

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