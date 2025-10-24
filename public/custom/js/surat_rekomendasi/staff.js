(function() {
    'use strict';

    // Check if Laravel routes are properly defined
    if (!window.Laravel || !window.Laravel.listData) {
        console.error('Laravel routes are not properly defined');
        return;
    }

    // First, define all your functions at the top
    const initializeDataTableRekom = (status, year) => {
        return $("#sr-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: `${window.Laravel.listData}?status=${status}&year=${year}`,
                type: 'GET',
                dataType: 'json',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                error: function(xhr) {
                    console.error('DataTables AJAX error:', xhr.responseText);
                    let msg = 'Terjadi kesalahan saat memuat data.';
                    try { var res = JSON.parse(xhr.responseText); if (res.error) msg = res.error; if (res.message) msg = res.message; } catch(e){}
                    Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                }
            },
            columns: [
                { data: "created_at", visible: false },
                { data: "DT_RowIndex" },
                { data: "nama" },
                { data: "nim" },
                { data: "program_studi" },
                { data: "tanggal_lulus" },
                { data: "nomor_ijazah" },
                { data: "status" },
                { data: "tanggal_submit", name: "created_at" },
                { data: "action" },
            ],
            columnDefs: [
                {
                    className: "text-center",
                    width: "3%",
                    targets: [1],
                },
                {
                    width: "10%",
                    targets: [3,8],
                },
                {
                    className: "btn-group-vertical",
                    targets: [9],
                },
                {
                    className: "text-wrap",
                    targets: [2],
                },
                {
                    className: "text-center",
                    targets: [5,6,7,8,9],
                },
            ],
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, 'All']
            ],
            order: [[0, "desc"]],
        });
    };

    // Then initialize your variables and event handlers
    let status_table = 'all';
    let year = new Date().getFullYear();

    // Initialize table only if element exists
    let tableRekom;

    if ($("#sr-datatable").length) {
        tableRekom = initializeDataTableRekom(status_table, year);
    }

    $(document).on('click', '.tahun-menu', function () {
        year = $(this).data('year');
        $('#tahunDropdown').html(year);
        tableRekom = initializeDataTableRekom(status_table, year);
    });

    $('#btn-export').click(function () {
        $('#form-export').attr('action', window.Laravel.export);
        $('#modalExport').modal('show');
    });

    $(document).on('click', '.status-menu', function () {
        status_table = $(this).data('status');
        $('#statusDropdown').html($(this).html());
        tableRekom = initializeDataTableRekom(status_table, year);
    });

    // No queue polling — controller does not provide queue_number; polling removed.

    $("#show_data").on("click", ".btn-detail", function () {
        let id = $(this).data("id");

        let url = window.Laravel.getData.replace(":id", id);

        $.ajax({
            url: url,
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                if (res.status) {
                    $("#detail-nama").html(": " + (res.data.user ? res.data.user.name : '-'));
                    $("#detail-nim").html(": " + (res.data.user ? res.data.user.nim : '-'));
                    $("#detail-prodi").html(": " + (res.data.user && res.data.user.prodis ? res.data.user.prodis.name : '-'));
                    $("#detail-permohonan").html(": " + (res.data.permohonan || '-'));
                    $("#detail-tanggal_lulus").html(": " + (res.data.tanggal_lulus || "-"));
                    $("#detail-nomor_ijazah").html(": " + (res.data.nomor_ijazah || "-"));

                    const suratUrl = res.data.surat_hasil_url || res.data.file_url;
                    if (suratUrl) {
                        $("#detail-file").attr("href", suratUrl).removeClass('disabled').removeAttr('aria-disabled');
                    } else {
                        $("#detail-file").attr("href", "#").addClass('disabled').attr('aria-disabled', 'true');
                    }

                    if (res.data.file_ijazah_url) {
                        $("#detail-file-ijazah").attr("href", res.data.file_ijazah_url).removeClass('disabled').removeAttr('aria-disabled');
                    } else {
                        $("#detail-file-ijazah").attr("href", '#').addClass('disabled').attr('aria-disabled', 'true');
                    }

                    if (res.data.file_transkrip_url) {
                        $("#detail-file-transkrip").attr("href", res.data.file_transkrip_url).removeClass('disabled').removeAttr('aria-disabled');
                    } else {
                        $("#detail-file-transkrip").attr("href", '#').addClass('disabled').attr('aria-disabled', 'true');
                    }

                    $("#detail-no").html(res.data.no_surat ? ': ' + res.data.no_surat : ': -');
                    $("#detail-catatan").html(res.data.catatan ? ': ' + res.data.catatan : ': -');
                    $("#detail-proses").html(res.data.tanggal_proses ? ': ' + res.data.tanggal_proses : ': -');
                    if (res.data.status) {
                        $("#detail-status").html(res.data.status.name);
                        $("#detail-status").attr("class", `btn ${res.data.status.color} btn-small`);
                    } else {
                        $("#detail-status").html('-');
                        $("#detail-status").attr("class", 'btn btn-secondary btn-small');
                    }

                    $("#modalDetail").modal("show");
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: res.message,
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                var err = JSON.parse(xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: err.message,
                    icon: "error",
                });
            },
        });
    });

    $("#show_data").on("click", ".btn-generate", function () {
        let id = $(this).data("id");

        let url = window.Laravel.generate.replace(":id", id);

        Swal.fire({
            title: "Menghasilkan dokumen...",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                Swal.close();
                if (res.status && res.url) {
                    window.open(res.url, "_blank");
                } else {
                    Swal.fire({ title: 'Gagal', text: res.message || 'Dokumen tidak tersedia', icon: 'error' });
                }
            },
            error: function (xhr, status, error) {
                Swal.close();
                let msg = 'Terjadi kesalahan saat menghasilkan dokumen';
                try { var err = JSON.parse(xhr.responseText); if (err.message) msg = err.message; } catch(e){}
                Swal.fire({ title: 'Gagal', text: msg, icon: 'error' });
            },
        });
    });

    $("#show_data").on("click", ".btn-proses", function () {
        let id = $(this).data("id");
        $("#form-proses").attr("action", window.Laravel.routeProses.replace(":id", id));
        $("#modalProses").modal("show");
    });

    $("#show_data").on("click", ".btn-edit", function () {
        let id = $(this).data("id");

        let url = window.Laravel.getData.replace(":id", id);
        let action = window.Laravel.updateData.replace(":id", id);

        $.ajax({
            url: url,
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                if (res.status) {
                    $("form#form-edit").attr("action", action);
                    $("#status_id-revisi").val(res.data.status_id || '');
                    $("#no_surat-revisi").val(res.data.no_surat || '');
                    $("#modalEdit").modal("show");

                    const statusVal = $('#status_id-revisi').val();
                    toggleNoSuratField(statusVal);
                    toggleUploadField(statusVal);
                    $('#status_id-revisi').trigger('change');
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: res.message,
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                var err = JSON.parse(xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: err.message,
                    icon: "error",
                });
            },
        });
    });

    function toggleNoSuratField(statusVal) {
        if (statusVal === '6') {
            $("#form-no-surat-edit").removeAttr("hidden");
        } else {
            $("#form-no-surat-edit").attr("hidden", true);
            $("#no_surat-revisi").val("");
        }
    }

    function toggleUploadField(statusVal) {
        if (statusVal === '9') {
            $("#form-file-edit").removeAttr("hidden");
        } else {
            $("#form-file-edit").attr("hidden", true);
            $("#file-revisi").val("");
        }
    }

    $('#status_id-revisi').on('change', function(){
        const val = $(this).val();
        toggleNoSuratField(val);
        toggleUploadField(val);
    });

    $("#form-proses").submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Apakah Anda yakin?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Proses!"
        }).then(function(result){
            if (!result.isConfirmed) return;

            let formData = new FormData(e.target);

            $.ajax({
                url: $(e.target).attr("action"),
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: formData,
                processData: false,
                contentType: false,
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
                    Swal.close();
                    if (res.status) {
                        $("#form-proses input").val("");
                        $("#form-proses textarea").val("");
                        $("#modalProses").modal("hide");
                        Swal.fire({
                            title: "Berhasil!",
                            text: res.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        tableRekom.ajax.reload();
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

        // form-edit for staff: only send status_id (and conditional no_surat/file)
        $("#form-edit").submit(function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Apakah inputan Anda sudah benar?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Sudah!"
            }).then(function(result){
                if (!result.isConfirmed) return;

                // Untuk staff, hanya kirim status_id
                let formData = new FormData();
                formData.append('_method', 'PUT');
                const statusVal = $('#status_id-revisi').val();
                formData.append('status_id', statusVal);
                if (statusVal === '6') {
                    formData.append('no_surat', $('#no_surat-revisi').val());
                }
                if (statusVal === '9') {
                    const file = $('#file-revisi')[0].files[0];
                    if (file) {
                        formData.append('file', file);
                    }
                }

                $.ajax({
                    url: $(e.target).attr("action"),
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
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
                        Swal.close();
                        if (res.status) {
                            $("#form-edit input").val("");
                            $("#form-edit textarea").val("");
                            $("#modalEdit").modal("hide");
                            Swal.fire({
                                title: "Berhasil!",
                                text: res.message,
                                icon: "success",
                            });
                            if (typeof tableRekom !== 'undefined' && tableRekom.ajax) tableRekom.ajax.reload();
                        } else {
                            Swal.fire({
                                title: "Gagal!",
                                text: res.message,
                                icon: "error",
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        console.error('Edit error:', xhr.responseText);

                        if (xhr.status === 422) {
                            var errors = {};
                            try {
                                errors = JSON.parse(xhr.responseText).errors || {};
                            } catch(e){}
                            var msgs = Object.values(errors).map(function(v){
                                return v.join(' ');
                            }).join('\n');
                            Swal.fire({
                                title: 'Validasi Gagal',
                                html: msgs || 'Harap periksa inputan Anda',
                                icon: 'warning'
                            });
                        } else {
                            var err = {};
                            try {
                                err = JSON.parse(xhr.responseText);
                            } catch(e){}
                            Swal.fire({
                                title: "Gagal!",
                                text: err.message || 'Terjadi kesalahan',
                                icon: "error",
                            });
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                });
            });
        });
    });
})();
