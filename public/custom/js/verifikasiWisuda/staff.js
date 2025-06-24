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
                // { data: "tanggal_submit" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "no_seri_ijazah" },
                { data: "periode_wisuda" },
                { data: "action" },
                { data: "catatan" },
            ],
            columnDefs: [
                { className: "text-center", width: "3%", targets: [1] },
                { className: "text-wrap", targets: [2] },
                { className: "btn-group-vertical", targets: [6] },
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
                // { data: "tanggal_submit" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "no_seri_ijazah" },
                { data: "periode_wisuda" },
                { 
                    data: "status.name",
                    render: function(data, type, row) {
                        return `<button type="button" class="${row.status.color} btn-sm mt-1" disabled>${data}</button>`;
                    }
                },
                { 
                    data: "action",
                    render: function(data, type, row) {
                        if (row.status_id == "2") { // Assuming 2 is "Diterima" status
                            return `
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm btn-terima" data-id="${row.id}" disabled>
                                        <i class="fa fa-check"></i> Terima
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak" data-id="${row.id}">
                                        <i class="fa fa-times"></i> Tolak
                                    </button>
                                </div>
                            `;
                        } else if (row.status_id == "3") { // Assuming 3 is "Ditolak" status
                            return `
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm btn-terima" data-id="${row.id}">
                                        <i class="fa fa-check"></i> Terima
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak" data-id="${row.id}" disabled>
                                        <i class="fa fa-times"></i> Tolak
                                    </button>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm btn-terima" data-id="${row.id}">
                                        <i class="fa fa-check"></i> Terima
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-tolak" data-id="${row.id}">
                                        <i class="fa fa-times"></i> Tolak
                                    </button>
                                </div>
                            `;
                        }
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", width: "3%", targets: [1] },
                { className: "text-wrap", targets: [2] },
                { className: "btn-group-vertical", targets: [7] },
            ],
            order: [[0, "desc"]],
        });
    };

    // Then initialize your variables and event handlers
    let status_table = $("#statusDropdown").data('status') || 'all';
    let year = $("#tahunDropdown").html();
    let year2 = $("#tahunWisudawanDropdown").length ? $("#tahunWisudawanDropdown").html() : $("#tahunDropdown").html();

    // Initialize both tables only if elements exist
    let table, wisudawanTable;
    
    if ($("#wisuda-datatable").length) {
        table = initializeDataTable(status_table, year);
    }
    
    if ($("#wisudawan-datatable").length && window.Laravel.listWisudawan) {
        wisudawanTable = initializeWisudawanDataTable(year2);
    }

    $(".tahun-menu").click(function () {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table = initializeDataTable(status_table, year);
    });
    $('#btn-import').click(function () {
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
                    if (table) {
                        table.ajax.reload();
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
    $('#btn-export-wisudawan').click(function () {
        if (window.Laravel.exportWisudawan) {
            $('#form-export-wisudawan').attr('action', window.Laravel.exportWisudawan);
            $('#modalExportWisudawan').modal('show');
        } else {
            Swal.fire({
                title: "Error!",
                text: "Export wisudawan tidak tersedia",
                icon: "error",
            });
        }
    });

    $(".status-menu").click(function () {
        status_table = $(this).data("status");
        $("#statusDropdown").html($(this).html());
        table = initializeDataTable(status_table, year);
    });    
    // Add year dropdown handler for wisudawan table
    $(".tahun-wisudawan-menu").click(function () {
        year2 = $(this).data("year");
        $("#tahunWisudawanDropdown").html(year2);
        if (wisudawanTable && window.Laravel.listWisudawan) {
            wisudawanTable = initializeWisudawanDataTable(year2);
        }
    });

    // Add Terima/Tolak button handlers only if routes exist and table exists
    if (window.Laravel.routeTerima && window.Laravel.routeTolak && $("#wisudawan-datatable").length) {
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
    }

})();
