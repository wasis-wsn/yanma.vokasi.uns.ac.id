const prodi_select2 = () => {
    return $('#prodi_id').select2({
        dropdownParent: $('#div_prodi_id'),
        theme: 'bootstrap-5',
        placeholder: "Pilih Prodi",
        ajax: {
            delay: 100,
            url: window.Laravel.getProdi,
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
                            text: item.name,
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
                return "Prodi Tidak Ditemukan. <button type='button' class='btn btn-xs btn-danger manual'>Klik Untuk Input Prodi secara Manual</button>";
            },
            inputTooShort: function () {
                return "Input minimal 3 huruf untuk menampilkan prodi";
            },
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    });
}

$(document).on('click', '.manual', function() {
    $("#prodi_id").select2("close");
    $('#prodi_id').select2("destroy");
    $('#prodi_id').attr('hidden', true);
    $('#prodi_id').removeAttr('required');
    $('#namaProdi').removeAttr('hidden');
    $('#namaProdi').attr('required', true);
})

const initializeDataTable = (status, year) => {
    return $("#legalisir-datatable").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: `${window.Laravel.listData}?status=${status}&year=${year}`,
        columns: [
            { data: "created_at", visible: false },
            { data: "DT_RowIndex" },
            { data: "tanggal_submit" },
            { data: "name" },
            { data: "nim" },
            { data: "prodi.name" },
            { data: "legalisir" },
            { data: "jumlah" },
            { data: "keperluan" },
            { data: "status_id" },
            { data: "action" },
            { data: "tanggal_ambil" },
            { data: "catatan" }
        ],
        columnDefs: [
            {
                target: 0,
                visible: false,
                searchable: false,
            },
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
                targets: [10],
            },
            {
                className: "text-wrap",
                targets: [3, 5, 6, 8],
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

$(".status-menu").click(function () {
    status_table = $(this).data("status");
    $("#statusDropdown").html($(this).html());
    table = initializeDataTable(status_table, year);
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

$('.btn-add').click(function () {
    let form = $('#form-tambah');
    form.trigger('reset');
    prodi_select2();
    $("#prodi_id").val('').trigger("change");
    $('#namaProdi').val('');
    $('#namaProdi').attr('hidden', true);
    $(".form-check-input").removeAttr("checked");
    let action = window.Laravel.store;
    form.attr("action", action);
    $("#modalTitle").html("Tambah Ajuan Legalisir");
    $('#modalTambah').modal('show');
})

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
                $("#detail-nama").text(": " + res.data.name);
                $("#detail-nim").text(": " + res.data.nim);
                $("#detail-prodi").text(": " + res.data.prodi.name);
                $("#detail-tahun_lulus").text(": " + res.data.tahun_lulus);
                $("#detail-no_wa").text(": " + res.data.no_wa);
                $("#detail-legalisir").text(": " + res.data.legalisir);
                $("#detail-jumlah").text(": " + res.data.jumlah);
                $("#detail-keperluan").text(": " + res.data.keperluan);
                $("#detail-catatan").text(": " + res.data.catatan);
                $("#detail-status").addClass(
                    `btn ${res.data.status.color} btn-sm`
                );
                $("#detail-status").text(res.data.status.name);

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

$("#show_data").on("click", ".btn-edit", function () {
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
                let action = window.Laravel.update.replace(":id", id);
                $("form#form-tambah").attr("action", action);
                $("#modalTitle").html("Edit Ajuan Legalisir");
                $("#name").val(res.data.name);
                $("#nim").val(res.data.nim);

                $('#namaProdi').val('');
                $('#namaProdi').attr('hidden', true);
                $('#namaProdi').removeAttr('required');
                $('#prodi_id').attr('required', true);
                prodi_select2();

                let oldProdi = $("<option selected='selected'></option>")
                    .val(res.data.prodi_id)
                    .text(res.data.prodi.name);
                $("#prodi_id").append(oldProdi).trigger("change");
                $(".form-check-input").removeAttr("checked");
                res.data.legalisir.forEach((e) => {
                    $(`#${e}`).attr("checked", "checked");
                });

                $("#no_wa").val(res.data.no_wa);
                $("#jumlah").val(res.data.jumlah);
                $("#keperluan").val(res.data.keperluan);
                $("#tahun_lulus").val(res.data.tahun_lulus);

                $("#modalTambah").modal("show");
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

$("#show_data").on("click", ".btn-delete", function () {
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
            let url = window.Laravel.deleteData.replace(":id", id);

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

$("#show_data").on("click", ".btn-proses", function () {
    let id = $(this).data("id");
    let status_id = $(this).data("status");
    let action = window.Laravel.routeProses.replace(":id", id);

    $("#form-proses").attr("action", action);
    $("#status_id").val(status_id);
    $("#modalProses").modal("show");
});

$('#status_id').change(function () {
    if ($(this).val() === '3') {
        $('#catatan-proses-ta').val('Silahkan ambil Legalisir di Front Office SV pada hari dan jam kerja');
    } else {
        $('#catatan-proses-ta').val('');
    }
});

$("#form-proses").submit(function (e) {
    e.preventDefault();
    let form = this;
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
                form.reset();
                $("#modalProses").modal("hide");
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
});

$("#form-tambah").submit(function (e) {
    e.preventDefault();
    let form = this;
    Swal.fire({
        title: "Apakah semua Inputan sudah benar?",
        text: "Pastikan juga Dokumen pemohon sesuai dengan ketentuan",
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
