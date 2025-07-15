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
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        // Use raw ID like in perpanjangan system
                        return `<input type="checkbox" class="form-check-input row-checkbox" value="${data}">`;
                    }
                },
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
                { className: "text-center", width: "3%", targets: [1, 2] },
                { className: "text-wrap", targets: [3] },
                { className: "btn-group-vertical", targets: [8] },
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
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        // Use raw ID like in perpanjangan system
                        return `<input type="checkbox" class="form-check-input row-checkbox-wisudawan" value="${data}">`;
                    }
                },
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
                { className: "text-center", width: "3%", targets: [1, 2] },
                { className: "text-wrap", targets: [3] },
                { className: "btn-group-vertical", targets: [8] },
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

        // Get additional data from the button that was clicked
        const activeButton = $('.btn-edit[data-id]').last(); // Get the last clicked button
        if (activeButton.length) {
            formData.append('tahun', activeButton.data('tahun'));
            formData.append('bulan', activeButton.data('bulan'));
        }

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
                        // text: response.message,
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
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

                // If no filename from headers, create default
                if (!filename) {
                    const tahun = $('select[name="tahun"]').val() || new Date().getFullYear();
                    filename = `Rekap_Data_Wisudawan_Tahun_${tahun}.xlsx`;
                }

                // Create download link
                let blob = new Blob([response]);
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = filename;
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
                let errorMessage = 'Terjadi kesalahan saat mengexport data';

                try {
                    if (xhr.responseText) {
                        let err = JSON.parse(xhr.responseText);
                        errorMessage = err.message || errorMessage;
                    }
                } catch (parseError) {
                    // If response is not JSON, use default message
                    errorMessage = 'Terjadi kesalahan saat mengexport data';
                }

                Swal.fire({
                    title: "Error!",
                    text: errorMessage,
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
    $(document).on('click', '.btn-edit[data-id]', function() {
        const $button = $(this);
        const id = $button.data('id');
        const nama = $button.data('nama');
        const tanggal = $button.data('tanggal');
        const active = $button.data('active');
        const bulan = $button.data('bulan');
        const tahun = $button.data('tahun');

        console.log('Edit clicked:', {id, nama, tanggal, active, bulan, tahun}); // Debugging

        $('#nama_bulan').val(nama || '');
        $('#tanggal_wisuda').val(tanggal || '');
        $('#is_active').prop('checked', active == 1);

        // Store tahun and bulan in the button for later use
        $('.btn-edit[data-id="' + id + '"]').attr('data-current-tahun', tahun || new Date().getFullYear());
        $('.btn-edit[data-id="' + id + '"]').attr('data-current-bulan', bulan || 1);

        if (!window.Laravel || !window.Laravel.updatePeriode) {
            console.error('Laravel.updatePeriode is not defined');
            return;
        }

        const action = window.Laravel.updatePeriode.replace(':id', id);
        $('#form-edit-periode').attr('action', action);
        $('#modalEditPeriode').modal('show');
    });

    // Form submit handler for periode
    $('#form-edit-periode').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        // Get data from the currently selected periode
        const currentAction = $(this).attr('action');
        const periodeId = currentAction.split('/').pop();
        const activeButton = $('.btn-edit[data-id="' + periodeId + '"]');

        if (activeButton.length) {
            const tahun = activeButton.data('current-tahun') || activeButton.data('tahun') || yearPeriode;
            const bulan = activeButton.data('current-bulan') || activeButton.data('bulan');

            formData.append('tahun', tahun);
            formData.append('bulan', bulan);

            console.log('Appending tahun:', tahun, 'bulan:', bulan);
        }

        // Check if tanggal_wisuda changed to show confirmation
        const originalDate = activeButton.data('tanggal');
        const newDate = $('#tanggal_wisuda').val();

        console.log('Form submission debug:', {
            originalDate: originalDate,
            newDate: newDate,
            hasNewDate: !!newDate,
            datesAreDifferent: originalDate !== newDate
        });

        // Show confirmation when there's a date
        if (newDate) {
            // Show confirmation dialog about status reset
            Swal.fire({
                title: 'Konfirmasi Perubahan Periode',
                html: `
                    <p>Anda akan mengubah periode wisuda.</p>
                    <p><strong>Perhatian:</strong> Semua mahasiswa di database akan direset statusnya menjadi "Belum Diproses" (kecuali yang sudah dalam status "Belum Diproses").</p>
                    <p>Apakah Anda yakin ingin melanjutkan?</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    formData.append('reset_status', '1');
                    console.log('User confirmed, submitting with reset_status = 1');
                    submitPeriodeForm(formData);
                }
            });
        } else {
            console.log('No date provided, submitting without reset');
            submitPeriodeForm(formData);
        }
    });

    function submitPeriodeForm(formData) {
        const action = $('#form-edit-periode').attr('action');

        // Debug: log all form data
        console.log('Submitting form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
                console.log('Response:', response);

                if (response.status) {
                    let message = response.message;
                    if (response.reset_count > 0) {
                        message += ` ${response.reset_count} mahasiswa telah direset statusnya.`;
                    } else if (response.reset_count === 0 && formData.get('reset_status')) {
                        message += ' Tidak ada mahasiswa yang perlu direset atau mahasiswa sudah dalam status "Belum Diproses".';
                    }

                    Swal.fire({
                        title: "Berhasil!",
                        text: message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                    $('#modalEditPeriode').modal('hide');
                    if (periodeTable) {
                        periodeTable.ajax.reload();
                    }
                    // Reload verification tables to show updated data
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
                console.error('AJAX Error:', xhr);
                let err = JSON.parse(xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: err.message || "Terjadi kesalahan",
                    icon: "error",
                });
            }
        });
    }

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
        let isEdit = $(this).hasClass('btn-primary'); // Check if it's edit button (primary) or process button (warning)

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

                    // Check if trying to edit a student without certificate serial number
                    if (isEdit && (!data.no_seri_ijazah || data.no_seri_ijazah.trim() === '')) {
                        Swal.fire({
                            title: "Peringatan!",
                            text: `Mahasiswa dengan NIM ${nim} belum mendapatkan nomor seri ijazah. Silakan input nomor seri ijazah terlebih dahulu sebelum melakukan edit.`,
                            icon: "warning",
                            confirmButtonText: "Mengerti",
                            confirmButtonColor: "#3085d6"
                        });
                        return; // Stop execution here
                    }

                    // Set form action
                    let url = window.Laravel.routeProses.replace(":id", id);
                    $('#form-proses').attr('action', url);

                    // Set modal title based on type and action
                    if (isEdit) {
                        if (type === 'wisudawan') {
                            $('#titleModalProses').text(`Edit Status Wisudawan - ${nim}`);
                        } else {
                            $('#titleModalProses').text(`Edit Status Verifikasi - ${nim}`);
                        }
                    } else {
                        if (type === 'wisudawan') {
                            $('#titleModalProses').text(`Proses Status Wisudawan - ${nim}`);
                        } else {
                            $('#titleModalProses').text(`Proses Verifikasi Wisuda - ${nim}`);
                        }
                    }

                    // Reset form first
                    $('#form-proses')[0].reset();

                    // Reset manual catatan checkbox
                    $('#manual_catatan').prop('checked', false);

                    // For edit mode, make verification fields editable; for process mode, keep them read-only
                    if (isEdit) {
                        // Edit mode - fields are editable
                        $('#no_seri_ijazah').prop('readonly', true).val(data.no_seri_ijazah || '');
                        $('#periode_wisuda').prop('readonly', true).val(data.periode_wisuda || '');
                        // Make fields required in edit mode
                        $('#no_seri_ijazah').prop('required', false); // Optional
                        $('#periode_wisuda').prop('required', false); // Optional                    } else {
                        // Process mode - fields are read-only
                        $('#no_seri_ijazah').prop('readonly', true).val(data.no_seri_ijazah || '');
                        $('#periode_wisuda').prop('readonly', true).val(data.periode_wisuda || '');
                        // Remove required attributes since fields are read-only
                        $('#no_seri_ijazah').prop('required', false);
                        $('#periode_wisuda').prop('required', false);                    }

                    // Show all fields
                    $('.form-v9').show();

                    // Populate status dropdown based on context and mode
                    let statusOptions = '';
                    if (isEdit) {
                        // Edit mode - show all available statuses
                        statusOptions = `
                        <option value="1">Belum Diproses</option>
                        <option value="2">Sudah Terverifikasi</option>
                        <option value="3">Tidak Terverifikasi</option>
                        `;
                    } else {
                        // Process mode - limited options based on context
                        if (type === 'wisudawan') {
                            statusOptions = `
                            <option value="2">Sudah Terverifikasi</option>
                            <option value="3">Tidak Terverifikasi</option>
                            `;
                        } else {
                            statusOptions = `
                            <option value="1">Belum Diproses</option>
                            <option value="3">Tidak Terverifikasi</option>
                            `;
                        }
                    }

                    $('#status_id').html(statusOptions);

                    // Set current status and notes
                    $('#status_id').val(data.status_id);
                    $('#catatan').val(data.catatan || '');

                    // If there's existing catatan, check manual mode
                    if (data.catatan && data.catatan.trim() !== '') {
                        // Check if the existing catatan matches any default notes
                        const defaultNotes = {
                            '1': 'Mohon konfirmasi kesediaan untuk mengikuti prosesi wisuda yang akan diselenggarakan.',
                            '2': 'Selamat! Data Anda telah terverifikasi. ',
                            '3': 'Data tidak dapat diverifikasi. Silakan hubungi bagian akademik untuk informasi lebih lanjut.',
                            '4': 'Anda telah terdaftar sebagai peserta wisuda. Informasi lebih lanjut akan disampaikan kemudian.',
                            '5': 'Anda tidak mengikuti wisuda pada periode ini.',
                            '6': 'Data Anda siap untuk dikonfirmasi. Silakan lakukan konfirmasi keikutsertaan wisuda.'
                        };

                        const currentStatusNote = defaultNotes[data.status_id];
                        if (data.catatan !== currentStatusNote) {
                            // If catatan doesn't match default, enable manual mode
                            $('#manual_catatan').prop('checked', true);
                            $('#catatan').attr('placeholder', 'Tulis catatan manual Anda di sini...');
                        }
                    }

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

    // Handle status change to set default notes
    $('#status_id').on('change', function() {
        const statusId = $(this).val();
        const manualCatatan = $('#manual_catatan').prop('checked');

        // Only auto-fill if manual catatan is not checked
        if (!manualCatatan) {
            const defaultNotes = {
                '1': 'Mohon konfirmasi kesediaan untuk mengikuti prosesi wisuda yang akan diselenggarakan.',
                '2': 'Selamat! Data Anda telah terverifikasi. ',
                '3': 'Data tidak dapat diverifikasi. Silakan hubungi bagian akademik untuk informasi lebih lanjut.',
                '4': 'Anda telah terdaftar sebagai peserta wisuda. Informasi lebih lanjut akan disampaikan kemudian.',
                '5': 'Anda tidak mengikuti wisuda pada periode ini.',
                '6': 'Data Anda siap untuk dikonfirmasi. Silakan lakukan konfirmasi keikutsertaan wisuda.'
            };

            // Set default note if available
            if (defaultNotes[statusId]) {
                $('#catatan').val(defaultNotes[statusId]);
            } else {
                $('#catatan').val(''); // Clear if no default note
            }
        }
    });

    // Handle manual catatan checkbox
    $('#manual_catatan').on('change', function() {
        const isManual = $(this).prop('checked');

        if (isManual) {
            // Clear catatan for manual input
            $('#catatan').val('').attr('placeholder', 'Tulis catatan manual Anda di sini...');
        } else {
            // Restore auto catatan based on current status
            $('#catatan').attr('placeholder', 'Catatan akan terisi otomatis sesuai status yang dipilih...');
            $('#status_id').trigger('change'); // Trigger status change to fill auto catatan
        }
    });

    // Bulk action functionality for verifikasi table
    $('#select-all').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
        updateBulkActionButton();
    });

    $('#wisuda-datatable').on('change', '.row-checkbox', function() {
        updateBulkActionButton();
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateBulkActionButton() {
        const checkedBoxes = $('.row-checkbox:checked').length;
        $('#btn-bulk-action').prop('disabled', checkedBoxes === 0);
    }

    $('#btn-bulk-action').click(function() {
        const selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
            $('#form-bulk-process input[name="selected_ids"]').val(selectedIds.join(','));
            $('#modalBulkProcess').modal('show');
        } else {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih minimal satu data untuk diproses',
                icon: 'warning'
            });
        }
    });

    // Bulk action functionality for wisudawan table
    $('#select-all-wisudawan').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox-wisudawan').prop('checked', isChecked);
        updateBulkActionButtonWisudawan();
    });

    $('#wisudawan-datatable').on('change', '.row-checkbox-wisudawan', function() {
        updateBulkActionButtonWisudawan();
        const totalCheckboxes = $('.row-checkbox-wisudawan').length;
        const checkedCheckboxes = $('.row-checkbox-wisudawan:checked').length;
        $('#select-all-wisudawan').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateBulkActionButtonWisudawan() {
        const checkedBoxes = $('.row-checkbox-wisudawan:checked').length;
        $('#btn-bulk-action-wisudawan').prop('disabled', checkedBoxes === 0);
    }

    $('#btn-bulk-action-wisudawan').click(function() {
        const selectedIds = [];
        $('.row-checkbox-wisudawan:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
            $('#form-bulk-process-wisudawan input[name="selected_ids"]').val(selectedIds.join(','));
            $('#modalBulkProcessWisudawan').modal('show');
        } else {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih minimal satu data untuk diproses',
                icon: 'warning'
            });
        }
    });

    // Handle bulk process form submission for verifikasi
    $('#form-bulk-process').submit(function(e) {
        e.preventDefault();

        const selectedIds = $('.row-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih minimal satu data untuk diproses',
                icon: 'warning'
            });
            return;
        }

        const formData = new FormData();
        formData.append('status_id', $('#form-bulk-process select[name="status_id"]').val());
        formData.append('catatan', $('#form-bulk-process textarea[name="catatan"]').val());
        formData.append('periode_wisuda', $('#form-bulk-process input[name="periode_wisuda"]').val());
        formData.append('selected_ids', selectedIds.join(','));
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: window.Laravel.bulkProcess,
            type: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Mohon Tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status) {
                    $('#modalBulkProcess').modal('hide');
                    $('#select-all').prop('checked', false);
                    $('.row-checkbox').prop('checked', false);
                    updateBulkActionButton();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    table.ajax.reload();
                    if (wisudawanTable) {
                        wisudawanTable.ajax.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses data',
                    icon: 'error'
                });
            }
        });
    });

    // Handle bulk process form submission for wisudawan
    $('#form-bulk-process-wisudawan').submit(function(e) {
        e.preventDefault();

        const selectedIds = $('.row-checkbox-wisudawan:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih minimal satu data untuk diproses',
                icon: 'warning'
            });
            return;
        }

        const formData = new FormData();
        formData.append('status_id', $('#form-bulk-process-wisudawan select[name="status_id"]').val());
        formData.append('catatan', $('#form-bulk-process-wisudawan textarea[name="catatan"]').val());
        formData.append('selected_ids', selectedIds.join(','));
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: window.Laravel.bulkProcess,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Mohon Tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status) {
                    $('#modalBulkProcessWisudawan').modal('hide');
                    $('#select-all-wisudawan').prop('checked', false);
                    $('.row-checkbox-wisudawan').prop('checked', false);
                    updateBulkActionButtonWisudawan();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    if (wisudawanTable) {
                        wisudawanTable.ajax.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses data',
                    icon: 'error'
                });
            }
        });
    });
})();
