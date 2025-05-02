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
        // { data: "tempat" },
        { data: "mulai_kegiatan" },
        { data: "status_id" },
        // { 
        //     data: "queue_number",
        //     className: "queue-info"
        // },
        { data: "catatan" },
        { data: "id" },
    ],
    columnDefs: [
        {
            className: "text-center",
            targets: [0],
        },
        {
            targets: [7],
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
    console.log(lpj);
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
                $("#catatan-revisi").val(res.data.catatan ? res.data.catatan : 'Catatan revisi akan muncul disini jika Anda diminta merevisi ajuan');
                $("#edit_nama_kegiatan").val(res.data.nama_kegiatan);
                $("#edit_mulai_kegiatan").val(res.data.mulai_kegiatan.substring(0, 10));
                $("#edit_selesai_kegiatan").val(res.data.selesai_kegiatan.substring(0, 10));
                $("#edit_penyelenggara").val(res.data.penyelenggara);
                $("#edit_tempat").val(res.data.tempat);
                $("#edit_delegasi").val(res.data.delegasi);
                $("#edit_jumlah_peserta").val(res.data.jumlah_peserta);
                $("#edit_dospem").val(res.data.dospem);
                $("#edit_nip_dospem").val(res.data.nip_dospem);
                $("#edit_nidn_dospem").val(res.data.nidn_dospem);
                $("#edit_unit_dospem").val(res.data.unit_dospem);
                // $("#detail-antrian").html(": " + res.data.queue_number);
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
                $("#detail-kegiatan").html(": " + res.data.nama_kegiatan);
                $("#detail-nama").html(": " + res.data.user.name);
                $("#detail-nim").html(": " + res.data.user.nim);
                $("#detail-prodi").html(": " + res.data.user.prodis.name);
                $("#detail-no-wa").html(": " + res.data.user.no_wa);
                $("#detail-tanggal-kegiatan").html(
                    ": " + res.data.tanggal_kegiatan
                );
                $("#detail-penyelenggara").html(": " + res.data.penyelenggara);
                $("#detail-tempat-kegiatan").html(": " + res.data.tempat);
                $("#detail-delegasi").html(": " + res.data.delegasi);
                $("#detail-peserta").html(": " + res.data.jumlah_peserta);
                $("#detail-dospem").html(": " + res.data.dospem);
                $("#detail-nip-dospem").html(": " + res.data.nip_dospem);
                $("#detail-nidn-dospem").html(": " + res.data.nidn_dospem);
                $("#detail-unit-dospem").html(": " + res.data.unit_dospem);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/surat_tugas/upload/${res.data.file}`
                );
                $("#detail-no").html(res.data.no_surat ? res.data.no_surat : ':');
                $("#detail-catatan").html(res.data.catatan ? res.data.catatan : ':');
                $("#detail-proses").html(res.data.tanggal_proses ? res.data.tanggal_proses : ':');
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
                        form.reset()
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
            // console.log(formData);

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

// function updateQueueNumbers() {
//     $.ajax({
//         url: '/st/update-queue',
//         type: 'GET',
//         success: function(res) {
//             if (res.status) {
//                 table.ajax.reload(null, false);
//                 if ($('#modalDetail').is(':visible')) {
//                     // Update juga di modal detail jika terbuka
//                     $("#detail-queue-number").text(res.user_queue);
//                     $("#detail-total-queue").text(res.total_waiting);
//                 }
//             }
//         }
//     });
// }