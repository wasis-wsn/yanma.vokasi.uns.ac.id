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
                { data: "status_id" },
                { data: "user.name" },
                { data: "user.nim" },
                { data: "user.prodis.name" },
                { data: "no_surat" },
                { data: "tanggal_ambil" },
                { data: "action" },
                { data: "catatan" }
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
                    className: "text-wrap",
                    targets: [3],
                }
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
        table.ajax.reload(null, false);
    }, 300000);

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
});