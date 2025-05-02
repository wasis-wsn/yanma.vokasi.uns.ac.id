const initializeDataTableSKL = (status_table, year, prodi_table) => {
    return $("#skl-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: window.Laravel.skl.listData,
            type: 'GET',
            data: function(d) {
                return $.extend({}, d, {
                    status: status_table,
                    year: year,
                    prodi: prodi_table
                });
            }
        },
    columns: [
        { data: "created_at", visible: false },
        {
            data: "id", // Using the raw ID value
            render: function(data) {
                return '<input type="checkbox" class="data-checkbox" value="' + data + '">';
            },
            orderable: false,
            searchable: false
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
            targets: [9],
        },
        {
            width: '5%',
            className: "text-wrap",
            targets: [2],
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
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTableSKL(status_table, year, prodi_table);
});

$(".prodi-menu").on("click", function (e) {
    e.preventDefault();

    var selectedProdi = $(this).data("status"); // Ambil data-status

    if (!selectedProdi) {
        selectedProdi = "all"; // Default ke 'all' jika undefined
    }

    $("#prodiDropdown").text($(this).text()); // Ubah teks tombol dropdown
    $("#prodiDropdown").data("status", selectedProdi); // Perbarui data-status

    // Pastikan DataTable diperbarui setelah perubahan filter
    table.destroy();
    table = initializeDataTableSKL(status_table, year, selectedProdi);
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

// Hapus semua input ids[] dari proses massal sebelumnya
$("#form-proses input[name='ids[]']").remove();

// Hapus dan tambahkan input id untuk proses single
$("#form-proses input[name='id']").remove();
$("#form-proses").append(`<input type="hidden" name="id" value="${id}">`);

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

$("#btn-proses-massal").on("click", function () {
let selectedIds = $(".data-checkbox:checked")
    .map(function () {
        return $(this).val();
    })
    .get();

if (selectedIds.length === 0) {
    Swal.fire("Oops!", "Silakan pilih minimal satu data!", "warning");
    return;
}

// Set form action ke route massal
$("#form-proses").attr("action", window.Laravel.skl.routeProses);

// Buat input hidden ids[]
$("#form-proses input[name='ids[]']").remove();
selectedIds.forEach((id) => {
    $("#form-proses").append(
        `<input type="hidden" name="ids[]" value="${id}">`
    );
});

$("#modalProses").modal("show");
});

$("#form-proses").submit(function (e) {
e.preventDefault();
let formData = new FormData(this);
    // Jika single update, tambahkan id ke formData
    if ($(this).attr("action").includes('proses/')) {
        let id = $(this).attr("action").split('/').pop();
        formData.append('id', id);
    }
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
