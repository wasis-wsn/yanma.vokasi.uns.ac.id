const initializeDataTable = (status, year) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "tanggal_submit" },
            { data: "tanggal_proses" },
            { data: "keperluan" },
            { data: "no_surat" },
            { data: "status_id" },
            { 
                data: "queue_number",
                className: "queue-info",
            },
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
                targets: [4,5],
            },
            {
                className: "btn-group-vertical",
                targets: [10],
            },
            {
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

let table = initializeDataTable(status_table, year);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTable(status_table, year);
});

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year);
});

// Cek update antrian setiap 30 detik
setInterval(function() {
    if ($('#modalDetail').is(':visible') || $('.dataTables_filter input').is(':focus')) {
        $.ajax({
            url: '/suket/queue-status',
            type: "GET",
            success: function(res) {
                if (res.status) {
                    // Update tabel
                    table.ajax.reload(null, false);
                    
                    // Update modal detail jika terbuka
                    if ($('#modalDetail').is(':visible')) {
                        $("#detail-queue-number").text(res.user_queue);
                        $("#detail-total-queue").text(res.total_waiting);
                    }
                }
            }
        });
    }
}, 30000);

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
                $("#detail-tahun_akademik").html(
                    ": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester
                );
                $("#detail-keperluan").html(": " + res.data.keperluan);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/surat_keterangan/upload/${res.data.file}`
                );
                $("#detail-queue-number").text(res.data.queue_number);
                $.ajax({
                    url: '/suket/queue-status',
                    type: 'GET',
                    success: function(queueRes) {
                        if (queueRes.status) {
                            $("#detail-total-queue").text(queueRes.total_waiting);
                        }
                    }
                });
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-no").html(": " + res.data.no_surat);
                $(".btn-tolak").attr("data-id", id);
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
                const canProses = ["1", "3", "4", "5", "6"];
                // console.log(canProses.includes(res.data.status_id));
                if (canProses.includes(res.data.status_id)) {
                    $("#tombol-proses").data("id", id);
                    $("#tombol-proses").removeAttr("hidden");
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
            $("#form-proses input[name='no_surat']").val(res.data.no_surat);
            $("#form-proses select[name='status_id']").val(res.data.status_id);
            const canChangeNoSurat = ["5","6"];
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
    const canChangeNoSurat = ['5','6'];
    if (canChangeNoSurat.includes($(this).val())) {
        $('#form-no-surat').removeAttr('hidden');
    } else {
        $('#form-no-surat').attr('hidden', true);
    }
    if ($(this).val() === '9') {
        $('#form-surat-hasil').removeAttr('hidden');
    } else {
        $('#form-surat-hasil').attr('hidden', true);
    }
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
                    timer: 1500
                });
                if (res.status_id && [5,6,7].includes(parseInt(res.status_id))) {
                    updateQueueNumbers();
                }
                table.ajax.reload();
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

function updateQueueNumbers() {
    $.ajax({
        url: '/suket/update-queue',
        type: 'GET',
        success: function(res) {
            if (res.status) {
                table.ajax.reload(null, false);
                if ($('#modalDetail').is(':visible')) {
                    // Update juga di modal detail jika terbuka
                    $("#detail-queue-number").text(res.user_queue);
                    $("#detail-total-queue").text(res.total_waiting);
                }
            }
        }
    });
}