/*
 *
 * Pengajuan TTD TA
 *
 */
const mhs_select2 = () => {
    return $('#mhs_id').select2({
        dropdownParent: $('#div_mhs_id'),
        theme: 'bootstrap-5',
        placeholder: "Pilih Mahasiswa",
        ajax: {
            delay: 100,
            url: window.Laravel.TA.getMhs,
            type: "GET",
            dataType: "json",
            data: function (params) {
                return {
                    q: $.trim(params.term),
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.nim + ' - ' + item.name,
                            id: item.id
                        }
                    })
                };
            },
            escapeMarkup: function(markup) {
                return markup;
            }
        },
        minimumInputLength: 3,
        language: {
            noResults: function() {
                return "Mahasiswa Tidak Ditemukan. Pastikan mahasiswa sudah melakukan registrasi di sistem!";
            },
            inputTooShort: function () {
                return "Input minimal 3 huruf untuk menampilkan mahasiswa";
            },
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    });
}

const initializeDataTableTA = (status, year_ta) => {
    return $("#ttdTA-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.TA.listData}?status=${status}&year=${year_ta}`,
        columns: [
            { data: "DT_RowIndex" },
            { data: "user.name" },
            { data: "user.nim" },
            { data: "status_id" },
            { data: "created_at" },
            { data: "tanggal_ambil" },
            { data: "action" },
            { data: "catatan" },
        ],
        columnDefs: [
            {
                className: "text-center",
                width: "3%",
                targets: [0],
            },
            {
                width: "5%",
                targets: [3],
            },
            {
                className: "btn-group-vertical",
                targets: [6],
            },
            {
                className: "text-wrap",
                targets: [1],
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
    });
};

let table_ta = initializeDataTableTA(status_table_ta, year_ta);

$(".tahun-ta").click(function () {
    year_ta = $(this).data("year");
    $("#tahunDropdown").html(year_ta);
    table_ta = initializeDataTableTA(status_table_ta, year_ta);
});

$(".status-ta").click(function () {
    status_table_ta = $(this).data("status");
    $("#status_ta").html($(this).html());
    table_ta = initializeDataTableTA(status_table_ta, year);
});

setInterval(function () {
    table_ta.ajax.reload(null, false); // user paging is not reset on reload
}, 30000);

$("#tambahTA").click(function (e) {
    mhs_select2();
    $("#mhs_id").val('').trigger('change');
    let action = window.Laravel.TA.routeAdd;
    $("#form-tambah-ta").attr('action', action);
    $("#modalTambahTA").modal('show');
});

$("#form-tambah-ta").submit(function (e) {
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
                $("#modalTambahTA").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });
                table_ta.ajax.reload();
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

$("#show_data_ta").on("click", ".btn-delete", function () {
    Swal.fire({
        title: "Anda yakin hapus ajuan?",
        text: "Ajuan yang dihapus tidak dapat dipulihkan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Hapus!",
    }).then((result) => {
        if (result.isConfirmed) {
            let id = $(this).data("id");
            let url = window.Laravel.TA.deleteData.replace(":id", id);

            $.ajax({
                url: url,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
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
                        Swal.fire({
                            title: "Berhasil!",
                            text: res.message,
                            icon: "success",
                        });
                        table_ta.ajax.reload();
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

$("#show_data_ta").on("click", ".btn-proses", function () {
    let id = $(this).data("id");
    let action = window.Laravel.TA.routeProses.replace(":id", id);

    $("#form-proses-ta").attr("action", action);
    $("#form-proses textarea").val("");
    $.ajax({
        url: window.Laravel.skl.getData.replace(":id", id),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            $("#status_ta_id").val(res.data.status_id);
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
    $("#modalProsesTA").modal("show");
});

$('#status_ta_id').change(function () {
    if ($(this).val() === '4') {
        $('#catatan-proses-ta').val('Silahkan mengambil Lembar Pengesahan TA di Front Office SV UNS');
    } else {
        $('#catatan-proses-ta').val('');
    }
});

$("#form-proses-ta").submit(function (e) {
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
                $("#modalProsesTA").modal("hide");
                Swal.fire({
                    title: "Berhasil!",
                    text: res.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });
                table_ta.ajax.reload();
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