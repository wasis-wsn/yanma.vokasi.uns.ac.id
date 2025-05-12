var table = $("#suket-datatable").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    ajax: window.Laravel.listData,
    columns: [
        { data: "DT_RowIndex" },
        { data: "created_at" },
        { data: "tanggal_proses" },
        { data: "nama_kegiatan" },
        { data: "ketua.name" },
        { data: "tempat" },
        { data: "mulai_kegiatan" },
        { data: "is_dana" },
        { data: "status_id" },
        { data: "catatan" },
        { data: "id" },
    ],
    columnDefs: [
        {
            className: "text-center",
            targets: [0],
        },
        {
            targets: [10],
            orderable: false,
            searchable: false,
        },
    ],
});

setInterval(function () {
    table.ajax.reload(null, false); // user paging is not reset on reload
}, 300000);

$(".selesai_kegiatan").change(function () {
    let selesai = $(this).val();
    let date = new Date(selesai);
    date.setDate(date.getDate() + 14);
    let lpj = date.toISOString().slice(0, 10);
    $(".tanggal_lpj").val(lpj);
});

$("#show_data").on("click", ".btn-edit", function () {
    let id = $(this).data("id");

    let action = window.Laravel.revisi.replace(":id", id);
    let url = window.Laravel.getData.replace(":id", id);

    $.ajax({
        url: url,
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (res.status) {
                $("form#form-edit").attr("action", action);
                $("#edit_catatan").val(res.data.catatan);
                $("#edit_nama_kegiatan").val(res.data.nama_kegiatan);

                let $oldKetua = $("<option selected='selected'></option>")
                    .val(res.data.ketua_id)
                    .text(
                        res.data.ketua.nim +
                            " - " +
                            res.data.ketua.name +
                            " - " +
                            res.data.ketua.prodis.name
                    );
                $("#edit_ketua_id").append($oldKetua).trigger("change");

                $("#edit_no_surat_ormawa").val(res.data.no_surat_ormawa);
                $("#edit_tanggal_surat").val(res.data.tanggal_surat);
                if (res.data.is_dana == 1) {
                    $('#edit_is_dana1').prop('checked', true);
                } else {
                    $('#edit_is_dana0').prop('checked', true);
                }
                $("#edit_mulai_kegiatan").val(res.data.mulai_kegiatan);
                $("#edit_selesai_kegiatan").val(res.data.selesai_kegiatan);
                $("#edit_tanggal_lpj").val(res.data.tanggal_lpj);
                $("#edit_tempat").val(res.data.tempat);
                $("#form-edit input[type='file']").val("");
                if (res.data.catatan == '' || res.data.catatan == undefined) {
                    $("#form-edit input[type='file']").attr('disabled', true);
                } else {
                    $("#form-edit input[type='file']").removeAttr('disabled');
                }

                $("#modalEdit").modal("show");
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
                $("#detail-pembina").html(": " + res.data.ormawa.pembina.name);
                $("#detail-ormawa").html(": " + res.data.ormawa.name);
                $("#detail-kegiatan").html(": " + res.data.nama_kegiatan);
                $("#detail-ketua").html(
                    ": " +
                        res.data.ketua.nim +
                        " - " +
                        res.data.ketua.name +
                        " - " +
                        res.data.ketua.prodis.name
                );
                $("#detail-no-ketua").html(": " + res.data.ketua.no_wa);
                $("#detail-no-ormawa").html(": " + res.data.no_surat_ormawa);
                $("#detail-tanggal-ormawa").html(
                    ": " + res.data.tanggal_surat2
                );
                $("#detail-is-dana").html(res.data.is_dana == '1' ? ': Mengajukan Dana' : ': Tidak Mengajukan Dana');
                $("#detail-tanggal-lpj").html(": " + res.data.tanggal_lpj2);
                $("#detail-tanggal-kegiatan").html(
                    ": " + res.data.tanggal_kegiatan
                );
                $("#detail-tempat-kegiatan").html(": " + res.data.tempat);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/sik/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tanggal_proses);
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

$("#show_data").on("click", ".btn-delete", function () {
    Swal.fire({
        title: "Anda yakin membatalkan ajuan?",
        text: "Ajuan yang dibatalkan tidak dapat dipulihkan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Batalkan Ajuan!",
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

function resetForm() {
    $('textarea[name="nama_kegiatan"]').val('');
    $('select[name="ketua_id"]').val('');
    $('input[name="no_surat_ormawa"]').val('');
    $('input[name="tanggal_surat"]').val('');
    $('input[name="is_dana"]').prop('checked', false);
    $('input[name="mulai_kegiatan"]').val('');
    $('input[name="selesai_kegiatan"]').val('');
    $('input[name="tanggal_lpj"]').val('');
    $('textarea[name="tempat"]').val('');
}

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
                        resetForm();
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

$("#form-edit").submit(function (e) {
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
                        resetForm();
                        $("#modalEdit").modal("hide");
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
