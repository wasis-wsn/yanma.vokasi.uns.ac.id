var table = $("#suket-datatable").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    ajax: window.Laravel.listData,
    columns: [
        { data: "created_at", visible: false },
        { data: "DT_RowIndex" },
        { data: "status_id" },
        // { 
        //     data: "queue_number",
        //     className: "queue-info"
        // },
        { data: "catatan" },
        { data: "tanggal_submit" },
        { data: "tanggal_proses" },
        { data: "tahun_akademik" },
        { data: "perpanjangan_ke" },
        { data: "action" },
    ],
    columnDefs: [
        {
            className: "text-center",
            width: "3%",
            targets: [1],
        },
    ],
    order: [[0, "desc"]],
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
                $("#edit_tahun_akademik").val(res.data.tahun_akademik.tahun_akademik);
                $("#edit_semester").val(res.data.semester.semester);
                $("#edit_perpanjangan_ke").val(res.data.perpanjangan_ke);
                $("#catatan-revisi").val(res.data.catatan);
                // $("#detail-antrian").html(": " + res.data.queue_number);
                $("#form-edit input[type='file']").val("");

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
                $("#detail-tahun-akademik").html(": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester);
                $("#detail-perpanjangan").html(": " + res.data.perpanjangan_ke);
                $("#detail-file").attr(
                    "href",
                    `${window.Laravel.baseUrl}/storage/perpanjangan/upload/${res.data.file}`
                );
                $("#detail-catatan").html(": " + res.data.catatan);
                $("#detail-proses").html(": " + res.data.tgl_proses);
                $("#detail-no").html(": " + res.data.no_surat);
                $(".btn-tolak").attr("data-id", id);
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