@extends('_template.master')

@section('title', 'Layanan')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="position-relative">
    @include('_template.navbar')
    <!-- Nav Header Component Start -->
    <div class="iq-navbar-header" style="height: 80px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{asset('back/assets/images/dashboard/top-header1.png')}}" alt="header" class="img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>          <!-- Nav Header Component End -->
    <!--Nav End-->

    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">List Layanan</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end px-4 pb-2">
                            <button class="btn-add btn btn-primary d-none d-md-inline-block">Tambah layanan</button>
                            <button class="btn-add btn btn-sm btn-primary d-md-none">Tambah layanan</button>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="show-table" class="table table-striped">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Kategori</th>
                                            <th>Nama</th>
                                            <th>url Mahasiswa</th>
                                            <th>url Staff</th>
                                            <th>Status</th>
                                            <th style="min-width: 100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="show_data">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.layanan.modal_tambah')

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    var table = $('#show-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: "{{route('layanan.list')}}",
        columns: [
            { data: "DT_RowIndex" },
            { data: "kategori_layanan.name" },
            { data: "name" },
            { data: "url_mhs" },
            { data: "url_staff" },
            { data: "is_active" },
            { data: "action" },
        ],
        columnDefs: [
            {
                searchable: false,
                orderable: false,
                className: "text-center",
                targets: 5,
            },{
                searchable: false,
                width: '10%',
                className: "text-center",
                targets: 0,
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
    });

    $(document).ready(function() {
        $('#keterangan').summernote({
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['color', ['color']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
            ]
        });
    });

    $('.btn-add').click(function (){
        let action = "{{route('layanan.store')}}";
        $('#form-tambah').attr('action', action);
        $('#modalTitle').html('Tambah Layanan');
        $('#name').val('');
        $('#kategori').val('');
        $('#urutan').val('');
        $('#url_mhs').val('');
        $('#url_same').prop('checked', false);
        $('#url_staff').val('');
        $('#file').val('');
        $('input[name="gate[]"]').prop('checked', false);
        $('#is_active').prop('checked', false);
        $('#keterangan').summernote('reset');
        $('#modalTambah').modal('show');
    });

    $('#url_same').click(function () {
        if ($(this).is(':checked')) {
            $('#url_staff').val($('#url_mhs').val());
        }
    })

    $('#show_data').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        let action = "{{route('layanan.update', ':id')}}";
        action = action.replace(':id', id);

        let url = "{{route('layanan.edit', ':id')}}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                if (res.status) {
                    $('#form-tambah').attr('action', action);
                    $('#kategori_layanan_id').val(res.data.kategori_layanan_id);
                    $('#name').val(res.data.name);
                    $('#urutan').val(res.data.urutan);
                    $('#url_mhs').val(res.data.url_mhs);
                    $('#url_staff').val(res.data.url_staff);
                    $('#file').val('');
                    // let selectedGate = res.data.gate;
                    $('input[name="gate[]"]').prop('checked', false);
                    if (Array.isArray(res.data.roles) && res.data.roles.length > 0) {
                        res.data.roles.forEach(function(role) {
                            $('input[name="gate[]"][value="' + role.id + '"]').prop('checked', true);
                        });
                    }
                    (res.data.is_active == '1') ? $('#is_active').prop('checked', true) : $('#is_active').prop('checked', false);
                    $('#keterangan').summernote('code', res.data.keterangan);
                    $('#modalTitle').html('Edit Layanan');
                    $('#modalTambah').modal('show');
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
        });
    });

    $("#show_data").on("click", ".btn-delete", function () {
        Swal.fire({
            title: "Anda yakin hapus Layanan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data("id");
                let url = "{{route('layanan.destroy', ':id')}}"
                url = url.replace(":id", id);

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
                            table.ajax.reload(null, false);
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
                    $("#modalTambah").modal("hide");
                    Swal.fire({
                        title: "Berhasil!",
                        text: res.message,
                        icon: "success",
                    });
                    table.ajax.reload(null, false);
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
</script>

@endpush