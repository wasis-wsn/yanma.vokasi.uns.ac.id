(function() {
    'use strict';

    // Check if Laravel routes are properly defined
    if (!window.Laravel || !window.Laravel.listData) {
        console.error('Laravel routes are not properly defined');
        return;
    }

    // First, define all your functions at the top
    const initializeDataTable = (status, year) => {
        return $("#wisuda-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
            columns: [
                { data: "created_at", visible: false },
                { data: "DT_RowIndex" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "no_seri_ijazah" },
                { data: "periode_wisuda" },
                { data: "status_id" },
                { data: "action" },
                { data: "catatan" },
            ],
            columnDefs: [
                { className: "text-center", width: "3%", targets: [1] },
                { className: "text-wrap", targets: [2] },
                { className: "btn-group-vertical", targets: [7] },
            ],
            order: [[0, "desc"]],
        });
    };

    const initializeWisudawanDataTable = (year) => {
        return $("#wisudawan-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listWisudawan}?year=${year}`,
            columns: [
                { data: "created_at", visible: false },
                { data: "DT_RowIndex" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "no_seri_ijazah" },
                { data: "periode_wisuda" },
                { data: "status_id" },
                { data: "action" },
                { data: "catatan" },
            ],
            columnDefs: [
                { className: "text-center", width: "3%", targets: [1] },
                { className: "text-wrap", targets: [2] },
                { className: "btn-group-vertical", targets: [7] },
            ],
            order: [[0, "desc"]],
        });
    };

    const initializePeriodeDataTable = (tahun) => {
        return $("#periode-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listPeriode}?tahun=${tahun}`,
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

    // Then initialize your variables and event handlers
    let status_table = $("#statusDropdown").data('status') || 'all';
    let year = $("#tahunDropdown").html();
    let year2 = $("#tahunWisudawanDropdown").length ? $("#tahunWisudawanDropdown").html() : $("#tahunDropdown").html();
    let yearPeriode = $("#tahunPeriodeDropdown").length ? $("#tahunPeriodeDropdown").html() : $("#tahunDropdown").html();

    // Initialize tables only if elements exist
    let table, wisudawanTable, periodeTable;

    if ($("#wisuda-datatable").length) {
        table = initializeDataTable(status_table, year);
    }

    if ($("#wisudawan-datatable").length && window.Laravel.listWisudawan) {
        wisudawanTable = initializeWisudawanDataTable(year2);
    }

    if ($("#periode-datatable").length && window.Laravel.listPeriode) {
        periodeTable = initializePeriodeDataTable(yearPeriode);
    }

    // Export handler
    $('#btn-export').click(function() {
        if (window.Laravel.export) {
            $('#form-export').attr('action', window.Laravel.export);
            $('#modalExport').modal('show');
        } else {
            Swal.fire({
                title: "Error!",
                text: "Export data tidak tersedia",
                icon: "error",
            });
        }
    });

    $(".tahun-menu").click(function() {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table = initializeDataTable(status_table, year);
    });

    $('#btn-import').click(function() {
        if (window.Laravel.import) {
            $('#form-import').attr('action', window.Laravel.import);
            $('#modalImport').modal('show');
        } else {
            Swal.fire({
                title: "Error!",
                text: "Import data tidak tersedia",
                icon: "error",
            });
        }
    });

    // Handle import form submission
    $('#form-import').on('submit', function(e) {
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
                    title: 'Mengimport data...',
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
                    $('#modalImport').modal('hide');
                    // Reload both tables
                    if (table) {
                        table.ajax.reload();
                    }
                    if (wisudawanTable) {
                        wisudawanTable.ajax.reload();
                    }
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
                    text: err.message || "Terjadi kesalahan saat mengimport data",
                    icon: "error",
                });
            }
        });
    });

    // Add export handler for wisudawan table
    $('#btn-export-wisudawan').click(function() {
        if (window.Laravel.export) {
            $('#form-export-wisudawan').attr('action', window.Laravel.export);
            $('#modalExportWisudawan').modal('show');
        } else {
            Swal.fire({
                title: "Error!",
                text: "Export wisudawan tidak tersedia",
                icon: "error",
            });
        }
    });

    // Handle export form submission for wisudawan
    $('#form-export-wisudawan').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Mengexport data...',
                    text: 'Data akan dihapus setelah export selesai',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response, status, xhr) {
                // Get filename from response headers
                let filename = '';
                let disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    let filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    let matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }

                // Create download link
                let blob = new Blob([response]);
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = filename || 'export.xlsx';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Swal.fire({
                    title: "Berhasil!",
                    text: "Data berhasil diexport dan dihapus dari database",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                });

                $('#modalExportWisudawan').modal('hide');

                // Reload both tables to show updated data
                if (table) {
                    table.ajax.reload();
                }
                if (wisudawanTable) {
                    wisudawanTable.ajax.reload();
                }
            },
            error: function(xhr) {
                let err = JSON.parse(xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: err.message || "Terjadi kesalahan saat mengexport data",
                    icon: "error",
                });
            }
        });
    });

    $(".status-menu").click(function() {
        status_table = $(this).data("status");
        $("#statusDropdown").html($(this).html());
        table = initializeDataTable(status_table, year);
    });

    // Add year dropdown handler for wisudawan table
    $(".tahun-wisudawan-menu").click(function() {
        year2 = $(this).data("year");
        $("#tahunWisudawanDropdown").html(year2);
        if (wisudawanTable && window.Laravel.listWisudawan) {
            wisudawanTable = initializeWisudawanDataTable(year2);
        }
    });

    // Add year dropdown handler for periode table
    $(".tahun-periode-menu").click(function() {
        yearPeriode = $(this).data("year");
        $("#tahunPeriodeDropdown").html(yearPeriode);
        if (periodeTable && window.Laravel.listPeriode) {
            periodeTable = initializePeriodeDataTable(yearPeriode);
        }
    });

    // Edit periode button handler
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const tanggal = $(this).data('tanggal');
        const active = $(this).data('active');

        $('#nama_bulan').val(nama);
        $('#tanggal_wisuda').val(tanggal);
        $('#is_active').prop('checked', active == 1);

        const action = window.Laravel.updatePeriode.replace(':id', id);
        $('#form-edit-periode').attr('action', action);
        $('#modalEditPeriode').modal('show');
    });

    // Form submit handler for periode
    $('#form-edit-periode').on('submit', function(e) {
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
                    $('#modalEditPeriode').modal('hide');
                    if (periodeTable) {
                        periodeTable.ajax.reload();
                    }
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

    // Add event handlers for accept/reject buttons
    $(document).on("click", ".btn-terima", function() {
        let id = $(this).data("id");
        if (!id) return;

        Swal.fire({
            title: 'Terima Wisudawan?',
            text: "Anda yakin ingin menerima wisudawan ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = window.Laravel.routeTerima.replace(":id", id);
                $.ajax({
                    url: url,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: res.message,
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500,
                            });
                            if (wisudawanTable) {
                                wisudawanTable.ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                title: "Gagal!",
                                text: res.message,
                                icon: "error",
                            });
                        }
                    },
                    error: function(xhr) {
                        var err = JSON.parse(xhr.responseText);
                        Swal.fire({
                            title: "Error!",
                            text: err.message || "Terjadi kesalahan",
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $(document).on("click", ".btn-tolak", function() {
        let id = $(this).data("id");
        if (!id) return;

        Swal.fire({
            title: 'Tolak Wisudawan?',
            text: "Anda yakin ingin menolak wisudawan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = window.Laravel.routeTolak.replace(":id", id);
                $.ajax({
                    url: url,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: res.message,
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500,
                            });
                            if (wisudawanTable) {
                                wisudawanTable.ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                title: "Gagal!",
                                text: res.message,
                                icon: "error",
                            });
                        }
                    },
                    error: function(xhr) {
                        var err = JSON.parse(xhr.responseText);
                        Swal.fire({
                            title: "Error!",
                            text: err.message || "Terjadi kesalahan",
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    // Process button handler (works for both regular verification and wisudawan)
    $(document).on("click", ".btn-proses", function() {
        let id = $(this).data("id");
        let nim = $(this).data("nim");
        let type = $(this).data("type"); // Check if it's for wisudawan

        if (!id) return;

        // First get the student data
        $.ajax({
            url: window.Laravel.getData.replace(':id', id),
            type: 'GET',
            beforeSend: function() {
                Swal.fire({
                    title: 'Memuat data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();

                if (response.status && response.data) {
                    let data = response.data;

                    // Set form action
                    let url = window.Laravel.routeProses.replace(":id", id);
                    $('#form-proses').attr('action', url);

                    // Set modal title based on type
                    if (type === 'wisudawan') {
                        $('#titleModalProses').text(`Proses Status Wisudawan - ${nim}`);
                    } else {
                        $('#titleModalProses').text(`Proses Verifikasi Wisuda - ${nim}`);
                    }

                    // Reset form first
                    $('#form-proses')[0].reset();

                    // Make all verification fields read-only and populate with existing data
                    $('#no_seri_ijazah').prop('readonly', true).val(data.no_seri_ijazah || '');
                    $('#periode_wisuda').prop('readonly', true).val(data.periode_wisuda || '');
                    $('#kode_akses').prop('readonly', true).val(data.kode_akses || '');

                    // Show all fields but make them read-only
                    $('.form-v9').show();

                    // Remove required attributes since fields are read-only
                    $('#no_seri_ijazah').prop('required', false);
                    $('#periode_wisuda').prop('required', false);
                    $('#kode_akses').prop('required', false);

                    // Populate status dropdown based on context
                    let statusOptions = '';
                    if (type === 'wisudawan') {
                        // For wisudawan table: only show status 1 and 3
                        statusOptions = `
                        <option value="2">Sudah Terverifikasi</option>
                        <option value="3">Tidak Terverifikasi</option>
                        `;
                    } else {
                        // For verification table: only show status 1 and 3
                        statusOptions = `
                        <option value="1">Belum Diproses</option>
                        <option value="3">Tidak Terverifikasi</option>
                        `;
                    }

                    $('#status_id').html(statusOptions);

                    // Set current status and notes
                    $('#status_id').val(data.status_id);
                    $('#catatan').val(data.catatan || '');

                    // Show modal
                    $('#modalProses').modal('show');
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Gagal memuat data mahasiswa",
                        icon: "error",
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let err = JSON.parse(xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: err.message || "Terjadi kesalahan",
                    icon: "error",
                });
            }
        });
    });

    // Handle form submission for process modal
    $('#form-proses').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Memproses...',
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
                    $('#modalProses').modal('hide');
                    if (table) {
                        table.ajax.reload();
                    }
                    if (wisudawanTable) {
                        wisudawanTable.ajax.reload();
                    }
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

    // Remove the status change handler since we don't want fields to be required anymore
    // Handle status change in process modal (simplified)
    $('#status_id').on('change', function() {
        // All fields remain visible and read-only regardless of status
        // No need to show/hide or change required attributes
    });

})();
