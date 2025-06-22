const initializeDataTableSKL = (status, year, prodi) => {
    return $("#skl-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.skl.listData}?status=${status}&year=${year}&prodi=${prodi}`,
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
            { data: "user.name" },
            { data: "user.nim" },
            { data: "nama_prodi" },
            { data: "tanggal_submit" },
            { data: "tanggal_proses" },
            { data: "status_id" },
            { data: "no_surat" },
            { data: "action" },
            { data: "tanggal_ambil" },
            { data: "catatan" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [1],
            },
            {
                width: "5%",
                targets: [4],
            },
            {
                className: "btn-group-vertical",
                targets: [10],
            },
            {
                width: '5%',
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

let table = initializeDataTableSKL(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.skl.export);
    $('#modalExport').modal('show');
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

// Add prodi filter handler
$(".prodi-menu").click(function () {
    prodi_table = $(this).data("prodi");
    $("#prodiDropdown").html($(this).html());
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 30000);

$("#show_data_skl").on("click", ".btn-detail", function () {
    let id = $(this).data("id");

    let url = window.Laravel.skl.getData.replace(":id", id);

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
                $("#detail-lembar-persetujuan").attr(
                    "href",
                    `${window.Laravel.skl.baseUrl}/storage/skl/upload/${res.data.lembar_revisi}`
                );
                $("#detail-ss-bukti").attr(
                    "href",
                    `${window.Laravel.skl.baseUrl}/storage/skl/upload/${res.data.ss_ajuan_skl}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
                let canProses = ['1', '2', '3', '4', '5', '6'];
                if (canProses.includes(res.data.status_id)) {
                    $("#tombol-proses").data("id", id);
                    $("#tombol-proses").attr("hidden", false);
                } else {
                    $("#tombol-proses").attr("hidden", true);
                }
                $("#modalDetailSKL").modal("show");
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

$("#show_data_skl").on("click", ".btn-proses", function () {
    showModalProses(this);
});

$("#tombol-proses").on("click", function () {
    $("#modalDetailSKL").modal("hide");
    showModalProses(this);
});

function showModalProses(p) {
    let id = $(p).data("id");

    action = window.Laravel.skl.routeProses.replace(":id", id);

    $("#form-proses").attr("action", action);
    $("#form-proses textarea").val("");
    $("#form-proses input[type='text']").val("");
    $.ajax({
        url: window.Laravel.skl.getData.replace(":id", id),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            $("#form-proses input[name='no_surat']").val(res.data.no_surat);
            $("#form-proses select[name='status_id']").val(res.data.status_id);
            const canChangeNoSurat = ["3", "4", "5"];
            if (canChangeNoSurat.includes(res.data.status_id)) {
                $("#form-no-surat").removeAttr("hidden");
            } else {
                $('#form-no-surat').attr('hidden', true);
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

$('#status_id').change(function () {
    const canChangeNoSurat = ['3','4','5'];
    if (canChangeNoSurat.includes($(this).val())) {
        $('#form-no-surat').removeAttr('hidden');
    } else if ($(this).val() === '6') {
        $('#form-no-surat').attr('hidden', true);
        $('#catatan-proses').val('Silahkan mengambil SKL di Front Office SV UNS');
    } else {
        $('#form-no-surat').attr('hidden', true);
    }
});

$("#form-proses").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let form = this;
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
                form.reset();
                $("#modalProses").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
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
});

// Update bulk action button state
function updateBulkActionButton() {
    const checkedBoxes = $('.row-checkbox:checked').length;
    $('#btn-bulk-action').prop('disabled', checkedBoxes === 0);
}

// Handle select all checkbox
$(document).on('change', '#select-all', function() {
    const isChecked = $(this).prop('checked');
    $('.row-checkbox').prop('checked', isChecked);
    updateBulkActionButton();
});

// Handle individual checkbox changes
$(document).on('change', '.row-checkbox', function() {
    updateBulkActionButton();
    // Update header checkbox state
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
});

// Handle bulk action button click
$(document).on('click', '#btn-bulk-action', function() {
    const selectedIds = [];
    $('.row-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length > 0) {
        $('#form-bulk-process input[name="selected_ids"]').val(selectedIds.join(','));
        $('#modalBulkProcess').modal('show');
    } else {
        Swal.fire({
            title: "Peringatan!",
            text: "Pilih minimal satu data untuk diproses",
            icon: "warning",
        });
    }
});

// Handle bulk process form submission
$(document).on('submit', '#form-bulk-process', function(e) {
    e.preventDefault();

    const selectedIds = $('.row-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        Swal.fire({
            title: "Peringatan!",
            text: "Pilih minimal satu data untuk diproses",
            icon: "warning",
        });
        return;
    }

    const formData = new FormData();
    formData.append('status_id', $('#form-bulk-process select[name="status_id"]').val());
    formData.append('catatan', $('#form-bulk-process textarea[name="catatan"]').val());
    formData.append('selected_ids', selectedIds.join(','));
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: window.Laravel.skl.bulkProcess,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            Swal.fire({
                title: "Mohon Tunggu",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        success: function(response) {
            $('#modalBulkProcess').modal('hide');
            $('#form-bulk-process')[0].reset();

            if (response.status) {
                Swal.fire({
                    title: "Berhasil!",
                    text: response.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });

                // Reset checkboxes
                $('#select-all').prop('checked', false);
                $('.row-checkbox').prop('checked', false);
                $('#btn-bulk-action').prop('disabled', true);

                // Reload table
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
            let errorMessage = 'Terjadi kesalahan saat memproses data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            Swal.fire({
                title: "Error!",
                text: errorMessage,
                icon: "error",
            });
        }
    });
});
