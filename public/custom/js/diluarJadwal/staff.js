$(document).ready(function() {
    let year = $("#tahunDropdown").html();
    let status_table = $("#statusDropdown").data('status') || 'all';
    let prodi_table = $("#prodiDropdown").data('prodi') || 'all';
        
    const initializeDataTable = (status, year, prodi) => {
        return $("#suket-datatable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${window.Laravel.listData}?status=${status}&year=${year}&prodi=${prodi}`,
            columns: [
                { data: "created_at", visible: false },
                { data: "DT_RowIndex" },
                { data: "tanggal_submit" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "nama_prodi" },
                { data: "no_surat" },
                // { data: "semester" },
                // { data: "tanggal_bayar" },
                { data: "status_id" },
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
                    targets: [4],
                },
                {
                    className: "btn-group-vertical",
                    targets: [9],
                },
                {
                    className: "text-wrap",
                    targets: [3],
                },
            ],
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"],
            ],
            order: [[0, "desc"]],
        });
    };

    let table = initializeDataTable(status_table, year, prodi_table);

    $(".tahun-menu").click(function () {
        year = $(this).data("year");
        $("#tahunDropdown").html(year);
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $("#btn-export").click(function () {
        $("#form-export").attr("action", window.Laravel.export);
        $("#modalExport").modal("show");
    });

    $(".status-menu").click(function () {
        status_table = $(this).data("status");
        $("#statusDropdown").html($(this).html());
        table = initializeDataTable(status_table, year, prodi_table);
    });

    $(".prodi-menu").click(function () {
        prodi_table = $(this).data("prodi");
        $("#prodiDropdown").html($(this).html());
        table = initializeDataTable(status_table, year, prodi_table);
    });

    setInterval(function () {
        table.ajax.reload(null, false); // user paging is not reset on reload
    }, 30000);

    $("#show_data").on("click", ".btn-detail", function () {
        let id = $(this).data("id");

        let url = window.Laravel.getData.replace(":id", id);

        $("#detail-izin-cuti").empty();

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
                    $("#detail-semester").html(": " + res.data.semester_romawi);
                    $("#detail-tahun-akademik").html(": " + res.data.tahun_akademik.tahun_akademik + ' - ' + res.data.semester.semester);
                    $("#detail-alasan").html(": " + res.data.alasan);
                    $("#detail-bayar").html(": " + res.data.tgl_bayar);
                    $("#detail-surat-permohonan").attr(
                        "href",
                        `${window.Laravel.baseUrl}/storage/diluar_jadwal/upload/surat_permohonan/${res.data.surat_permohonan}`
                    );
                    $("#detail-bukti-bayar-ukt").attr(
                        "href",
                        `${window.Laravel.baseUrl}/storage/diluar_jadwal/upload/bukti_bayar_ukt/${res.data.bukti_bayar_ukt}`
                    );
                    let detail_izin_cuti = res.data.izin_cuti
                        ? `: <a href="${window.Laravel.baseUrl}/storage/diluar_jadwal/upload/izin_cuti/${res.data.izin_cuti}" target="_blank" class="btn btn-primary btn-small"><i class="fa fa-file"></i> Lihat File</a>`
                        : `: Tidak memiliki Izin Cuti`;
                    $("#detail-izin-cuti").append(detail_izin_cuti);
                    $("#detail-catatan").html(
                        res.data.catatan ? ": " + res.data.catatan : ":"
                    );
                    $("#detail-proses").html(": " + res.data.tgl_proses);
                    $("#detail-no").html(
                        res.data.no_surat ? ": " + res.data.no_surat : ":"
                    );
                    $(".btn-tolak").attr("data-id", id);
                    $("#detail-status").html(res.data.status.name);
                    $("#detail-status").attr(
                        "class",
                        `btn ${res.data.status.color} btn-small`
                    );
                    let canProses = ["1", "3", "4"];
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
                const canChangeNoSurat = ["3", "4", "6"];
                const isSelesai = res.data.status_id === "6";
                
                // Handle no surat visibility
                if (canChangeNoSurat.includes(res.data.status_id)) {
                    $('#form-no-surat').removeAttr('hidden');
                } else {
                    $('#form-no-surat').attr('hidden', true);
                }
                
                // Handle surat hasil visibility
                if (isSelesai) {
                    $('#form-surat-hasil').removeAttr('hidden');
                } else {
                    $('#form-surat-hasil').attr('hidden', true);
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

    $("#status_id").change(function () {
        const canChangeNoSurat = ["3", "4", "6"];
        const isSelesai = $(this).val() === "6"; // Assuming 6 is the ID for "Selesai" status
        
        // Handle no surat visibility
        if (canChangeNoSurat.includes($(this).val())) {
            $('#form-no-surat').removeAttr('hidden');
        } else {
            $('#form-no-surat').attr('hidden', true);
        }
        
        // Handle surat hasil visibility
        if (isSelesai) {
            $('#form-surat-hasil').removeAttr('hidden');
        } else {
            $('#form-surat-hasil').attr('hidden', true);
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
                        timer: 1500,
                    });
                    table.ajax.reload();
                    refreshCount();
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
});