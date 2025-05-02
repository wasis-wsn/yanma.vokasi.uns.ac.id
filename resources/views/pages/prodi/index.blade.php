@extends('_template.master')

@section('title', 'Prodi')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
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
                                <h4 class="card-title">Prodi List</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end px-4">
                            <button class="btn-add btn btn-primary d-none d-md-inline-block">Tambah prodi</button>
                            <button class="btn-add btn btn-sm btn-primary d-md-none">Tambah prodi</button>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="user-list-table" class="table table-striped">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Program Studi</th>
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
    @include('pages.prodi.modal_tambah')

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    var table = $('#user-list-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: "{{route('prodi.list')}}",
        columns: [
            { data: "DT_RowIndex" },
            { data: "name" },
            { data: "action" },
        ],
        columnDefs: [
            {
                searchable: false,
                orderable: false,
                className: "text-center",
                targets: 2,
            },{
                searchable: false,
                className: "text-center",
                targets: 0,
            },
        ],
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, 'All']
        ],
        order: [[1, 'desc']]
    });

    $('.btn-add').click(function () {
        let action = "{{route('prodi.store')}}";
        $('#form-tambah').attr('action', action);
        $('#form-tambah input').val('');
        $('#modalTitle').html('Tambah Prodi');
        $('button#save').html('Tambah');
        $('#modalTambah').modal('show');
    })

    $('#show_data').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        let action = "{{route('prodi.update', ':id')}}";
        let url = "{{route('prodi.edit', ':id')}}";
        action = action.replace(':id', id);
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                if (res.status) {
                    $('#form-tambah').attr('action', action);
                    $('#form-tambah input').val(res.data.name);
                    $('#modalTitle').html('Edit Prodi');
                    $('button#save').html('Simpan');
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
        })
    })

    $("#show_data").on("click", ".btn-delete", function () {
        Swal.fire({
            title: "Anda yakin hapus prodi?",
            text: "Prodi yang dihapus tidak dapat dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data("id");
                let url = "{{route('prodi.destroy', ':id')}}"
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
                    $("#form-tambah input").val("");
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
    });
</script>

@endpush