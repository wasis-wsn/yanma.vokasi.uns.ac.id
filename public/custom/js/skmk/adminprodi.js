const initializeDataTable = (status_table, year, prodi_table) => {
    return $("#suket-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: window.Laravel.listData,
            type: 'GET',
            data: function(d) {
                // Add custom parameters
                return $.extend({}, d, {
                    status: status_table,
                    year: year,
                    prodi: prodi_table
                });
            }
        },
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "created_at" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "nama_prodi" },
            { data: "no_surat" },
            { data: "status_id" },
            // { 
            //     data: "queue_number",
            //     className: "queue-info"
            // },
            { data: "catatan" },
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
                targets: [2],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
    });
};

let table = initializeDataTable(status_table, year, prodi_table);

$(".tahun-menu").click(function () {
    year = $(this).data("year");
    $("#tahunDropdown").html(year);
    table = initializeDataTable(status_table, year, prodi_table);
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

$('#btn-export').click(function () {
    $('#form-export').attr('action', window.Laravel.export);
    $('#modalExport').modal('show');
});

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
                $("#detail-semester").html(": " + res.data.semester_romawi);
                // $("#detail-antrian").html(": " + res.data.queue_number);
                $("#detail-tahun_akademik").html(
                    ": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester
                );
                $("#detail-nama_ortu").html(": " + res.data.nama_ortu);
                $("#detail-nip_ortu").html(": " + res.data.nip_ortu);
                $("#detail-pangkat_ortu").html(": " + res.data.pangkat_ortu);
                $("#detail-instansi_ortu").html(": " + res.data.instansi_ortu);
                $("#detail-alamat_instansi").html(
                    ": " + res.data.alamat_instansi
                );
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/skmk/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tanggal_proses);
                $("#detail-no").html(": " + res.data.no_surat);
                $("#detail-status").html(res.data.status.name);
                $("#detail-status").attr(
                    "class",
                    `btn ${res.data.status.color} btn-small`
                );
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