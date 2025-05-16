$(document).ready(function() {
    let year = $("#tahunDropdown").html();
    let status_table = $("#statusDropdown").data('status') || 'all';
    let prodi_table = $("#prodiDropdown").data('prodi') || 'all';

    const bulkProcessModal = new bootstrap.Modal(document.getElementById('modalBulkProcess'));

    const initializeDataTable = (status, year, prodi) => {
        return $("#suket-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listData}?status=${status}&year=${year}&prodi=${prodi}`,
            columns: [
                { data: "created_at", visible: false },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<input type="checkbox" class="form-check-input row-checkbox" value="' + data + '">';
                    }
                },
                { data: "DT_RowIndex" },
                { data: "tanggal_submit" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "nama_prodi" },
                { data: "status_id" },
                { data: "catatan" },
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
                    targets: [4],
                },
                {
                    className: "btn-group-vertical",
                    targets: [8],
                },
                {
                    className: "text-wrap",
                    targets: [3],
                },
            ],
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, 'All']
            ],
            order: [[0, "desc"]],
        });
    };

    let table = initializeDataTable(status_table, year, prodi_table);

    $(".tahun-menu").click(function () {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $("#btn-export").click(function () {
        $("#form-export").attr("action", window.Laravel.export);
        $("#modalExport").modal("show");
    });

    $(".status-menu").click(function () {
        status_table = $(this).data("status");
        $("#statusDropdown").html($(this).html());
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $(".prodi-menu").click(function () {
        prodi_table = $(this).data("prodi");
        $("#prodiDropdown").html($(this).html());
        table = initializeDataTable(status_table, year, prodi_table);
    });

    setInterval(function () {
        table.ajax.reload(null, false); // user paging is not reset on reload
    }, 300000);

    $("#show_data").on("click", ".btn-detail", function () {
        let id = $(this).data("id");

        let url = window.Laravel.getData.replace(":id", id);

        $("#detail-izin-cuti").empty();

        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                if (res.status) {
                    $("#detail-nama").html(": " + res.data.user.name);
                    $("#detail-nim").html(": " + res.data.user.nim);
                    $("#detail-prodi").html(": " + res.data.user.prodis.name);
                    $("#detail-tahun-akademik").html(": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester);
                    $("#detail-alasan").html(": " + res.data.alasan);
                    $("#detail-file").attr(
                        "href",
                        `${window.Laravel.baseUrl}/storage/penundaan/upload/${res.data.file}`
                    );
                    $("#detail-catatan").html(
                        res.data.catatan ? ": " + res.data.catatan : ":"
                    );
                    $("#detail-proses").html(": " + res.data.tgl_proses);
                    $("#detail-no").html(
                        res.data.no_surat ? ": " + res.data.no_surat : ":"
                    );
                    $(".btn-tolak").attr("data-id", id);
                    $("#detail-status").html(res.data.status.name);
                    $("#detail-status").attr(
                        "class",
                        `btn ${res.data.status.color} btn-small`
                    );
                    // let canProses = [1, 3, 4];
                    let canProses = ["1", "3", "4"];
                    if (canProses.includes(res.data.status_id)) {
                        $("#tombol-proses").data("id", id);
                        $("#tombol-proses").attr("hidden", false);
                    } else {
                        $("#tombol-proses").attr("hidden", true);
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

    $("#show_data").on("click", ".btn-proses", function () {
        showModalProses(this);
    });

    $("#tombol-proses").on("click", function () {
        $("#modalDetail").modal("hide");
        showModalProses(this);
    });

    function showModalProses(p) {
        let id = $(p).data("id");

        action = window.Laravel.routeProses.replace(":id", id);

        $("form#form-proses").attr("action", action);
        $("#form-proses textarea").val("");
        $("#form-proses input[type='text']").val("");
        $("#form-proses input[type='file']").val("");
        $.ajax({
            url: window.Laravel.getData.replace(":id", id),
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                $("#form-proses select[name='status_id']").val(res.data.status_id);
                const isSelesai = res.data.status_id === "6";

                // Handle surat hasil visibility
                if (isSelesai) {
                    $('#form-surat-hasil').removeAttr('hidden');
                } else {
                    $('#form-surat-hasil').attr('hidden', true);
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

        $("#modalProses").modal("show");
    }

    $('#tahun_akademik').on('change', function() {
        let value = $(this).val();
        $('input[name="tahun_akademik_id"]').val(value);
    });
    $('#semester').on('change', function() {
        let value = $(this).val();
        $('input[name="semester_id"]').val(value);
    });

    $('#status_id').change(function () {
        const isSelesai = $(this).val() === "6"; // Assuming 6 is the ID for "Selesai" status

        // Handle surat hasil visibility
        if (isSelesai) {
            $('#form-surat-hasil').removeAttr('hidden');
        } else {
            $('#form-surat-hasil').attr('hidden', true);
        }
    });

    $("#form-setting").submit(function (e) {
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
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            success: function (res) {
                if (res.status) {
                    $("#modalJadwal").modal("hide");
                    Swal.fire({
                        title: "Berhasil!",
                        text: res.message,
                        icon: "success",
                    });
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

    $("#form-tambah").submit(function (e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: "Apakah inputan Anda sudah benar?",
            text: "Pastikan bahwa data yang Anda inputkan sudah benar",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Sudah!",
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr("action"),
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    data: formData,
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
                        if (res.status) {
                            form.reset();
                            $("#modalTambah").modal("hide");
                            Swal.fire({
                                title: "Berhasil!",
                                text: res.message,
                                icon: "success",
                            });
                            table.ajax.reload();
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
            }
        });
    });

    $("#form-proses").submit(function (e) {
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
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            success: function (res) {
                if (res.status) {
                    $("#form-proses input").val("");
                    $("#form-proses textarea").val("");
                    $("#modalProses").modal("hide");
                    Swal.fire({
                        title: "Berhasil!",
                        text: res.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    table.ajax.reload();
                    refreshCount();
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

    // Handle bulk action button state
    $('#suket-datatable').on('change', 'input[name="ids"]', function() {
        const checkedBoxes = $('input[name="ids"]:checked');
        $('#btn-bulk-action').prop('disabled', checkedBoxes.length === 0);
    });

    // Select/deselect all checkboxes
    $('#select-all').on('change', function() {
        $('input[name="ids"]').prop('checked', $(this).prop('checked'));
        $('#btn-bulk-action').prop('disabled', !$(this).prop('checked'));
    });

    // Handle select all checkbox
    $('#select-all').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
        updateBulkActionButton();
    });

    // Handle individual checkbox changes
    $('#suket-datatable').on('change', '.row-checkbox', function() {
        updateBulkActionButton();
        // Update header checkbox state
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Update bulk action button state
    function updateBulkActionButton() {
        const checkedBoxes = $('.row-checkbox:checked').length;
        $('#btn-bulk-action').prop('disabled', checkedBoxes === 0);
    }

    // Handle bulk action button click
    $('#btn-bulk-action').click(function() {
        const selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
            $('#form-bulk-process input[name="selected_ids"]').val(selectedIds.join(','));
            const bulkProcessModal = new bootstrap.Modal(document.getElementById('modalBulkProcess'));
            bulkProcessModal.show();
        } else {
            Swal.fire({
                title: 'Peringatan',
                text: 'Pilih minimal satu data untuk diproses',
                icon: 'warning'
            });
        }
    });

    // Handle bulk process form submission
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
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error response:', xhr.responseJSON);
                Swal.fire({
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses data',
                    icon: 'error'
                });
            }
        });
    });
});
