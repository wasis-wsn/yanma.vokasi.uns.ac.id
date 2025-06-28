(function() {
    'use strict';

    let year = $("#tahunDropdown").html();

    const initializeDataTable = (tahun) => {
        return $("#periode-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listData}?tahun=${tahun}`,
            columns: [
                { data: "DT_RowIndex" },
                { data: "nama_bulan" },
                { data: "tanggal_wisuda" },
                { data: "is_active" },
                { data: "action" }
            ],
            columnDefs: [
                { className: "text-center", width: "5%", targets: [0] },
                { className: "text-center", targets: [2, 3, 4] }
            ],
            order: [[0, "asc"]],
            paging: false,
            searching: false,
            info: false
        });
    };

    let table = initializeDataTable(year);

    // Year dropdown handler
    $(".tahun-menu").click(function() {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table = initializeDataTable(year);
    });

    // Edit button handler
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const tanggal = $(this).data('tanggal');
        const active = $(this).data('active');

        $('#nama_bulan').val(nama);
        $('#tanggal_wisuda').val(tanggal);
        $('#is_active').prop('checked', active == 1);

        const action = window.Laravel.update.replace(':id', id);
        $('#form-edit').attr('action', action);
        $('#modalEdit').modal('show');
    });

    // Form submit handler
    $('#form-edit').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    $('#modalEdit').modal('hide');
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        title: "Gagal!",
                        text: response.message,
                        icon: "error",
                    });
                }
            },
            error: function(xhr) {
                let err = JSON.parse(xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: err.message || "Terjadi kesalahan",
                    icon: "error",
                });
            }
        });
    });

})();
