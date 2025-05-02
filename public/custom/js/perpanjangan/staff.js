const initializeDataTable = (status_table, year, prodi_table) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: window.Laravel.listData,
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
            { data: "tanggal_submit" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "nama_prodi" },
            { data: "no_surat" },
            { data: "tahun_akademik" },
            { data: "perpanjangan_ke" },
            { data: "status_id" },
            // { 
            //     data: "queue_number",
            //     className: "queue-info",
            // },
            { data: "catatan" },
            { data: "action" }
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
                targets: [11],
                // targets: [9],
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

var year = $("#tahunDropdown").html();
var status_table = $("#statusDropdown").data('status');
var prodi_table = $("#prodiDropdown").data('prodi') || "all";

let table = initializeDataTable(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTable(status_table, year, prodi_table);
});

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year, prodi_table);
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
    table = initializeDataTable(status_table, year, selectedProdi);
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

$("#show_data").on("click", ".btn-detail", function () {
    let id = $(this).data("id");

    let url = window.Laravel.getData.replace(":id", id);

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
                $("#detail-email").html(": " + res.data.user.email);
                $("#detail-tahun-akademik").html(": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester);
                $("#detail-perpanjangan").html(": " + res.data.perpanjangan_ke);
                // $("#detail-queue-number").text(res.data.queue_number);
                // $.ajax({
                //     url: '/perpanjangan/queue-status',
                //     type: 'GET',
                //     success: function(queueRes) {
                //         if (queueRes.status) {
                //             $("#detail-total-queue").text(queueRes.total_waiting);
                //         }
                //     }
                // });
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/perpanjangan/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tgl_proses);
                $("#detail-no").html(": " + res.data.no_surat);
                // $(".btn-tolak").attr("data-id", id);
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
                // let canProses = [1, 3, 4];
                let canProses = ["1", "3", "4"];
                // console.log(id);
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
    let id = $(this).data("id");
    showModalProses(id);
});

$("#tombol-proses").on("click", function () {
    $("#modalDetail").modal("hide");
    let id = $(this).data("id");
    // console.log(id);
    showModalProses(id);
});

function showModalProses(id) {
    let action = window.Laravel.routeProses.replace(":id", id);
    let url = window.Laravel.getData.replace(":id", id);

    $("form#form-proses").attr("action", action);

    // Hapus semua input ids[] dari proses massal sebelumnya
    $("#form-proses input[name='ids[]']").remove();

    // Hapus dan tambahkan input id untuk proses single
    $("#form-proses input[name='id']").remove();
    $("#form-proses").append(`<input type="hidden" name="id" value="${id}">`);

    // Reset input form
    $("#form-proses textarea").val("");
    $("#form-proses input[type='text']").val("");
    $("#form-proses input[type='file']").val("");

    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            $("#form-proses input[name='no_surat']").val(res.data.no_surat);
            $("#form-proses select[name='status_id']").val(res.data.status_id);

            const canChangeNoSurat = ["3", "4", "6"];
            if (canChangeNoSurat.includes(res.data.status_id)) {
                $('#form-no-surat').removeAttr('hidden');
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
    const canChangeNoSurat = ["3", "4", "6"];
    if (canChangeNoSurat.includes($(this).val())) {
        $('#form-no-surat').removeAttr('hidden');
    } else {
        $('#form-no-surat').attr('hidden', true);
    }
});

$('#tahun_akademik').on('change', function() {
    let value = $(this).val();
    $('input[name="tahun_akademik_id"]').val(value);
});
$('#semester').on('change', function() {
    let value = $(this).val();
    $('input[name="semester_id"]').val(value);
});

$("#form-setting").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    console.log("submit");
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
                        $("#form-tambah input#perpanjangan_ke").val("");
                        $("#form-tambah input[type='file']").val("");
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
    $("#form-proses").attr("action", window.Laravel.routeProses);

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
                // if (res.status_id && [5,6,7].includes(parseInt(res.status_id))) {
                //     updateQueueNumbers();
                // }
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

// function updateQueueNumbers() {
//     $.ajax({
//         url: window.Laravel.updateQueue, // Menggunakan route dari object Laravel
//         type: 'GET',
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function(res) {
//             if (res.status) {
//                 table.ajax.reload(null, false);
//                 if ($('#modalDetail').is(':visible')) {
//                     $("#detail-queue-number").text(res.current_queue);
//                     $("#detail-total-queue").text(res.total_waiting);
//                 }
//             }
//         },
//         error: function(xhr) {
//             console.error('Error updating queue:', xhr.responseText);
//             toastr.error('Gagal memperbarui antrian');
//         }
//     });
// }