@extends('_template.master')

@section('title', 'Berita')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
<style>
    .card-clickable {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .card-clickable:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
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

    <div class="container-fluid content-inner mt-n5 py-0">
        <div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">List Berita</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end px-4 pb-2">
                            <button class="btn-add btn btn-primary d-none d-md-inline-block">Tambah Berita</button>
                            <button class="btn-add btn btn-sm btn-primary d-md-none">Tambah Berita</button>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="berita-list-table" class="table table-striped w-100">
                                    <thead>
                                        <tr class="light">
                                            <th>#</th>
                                            <th>Judul</th>
                                            <th>Gambar</th>
                                            <th>Deskripsi</th>
                                            <th>PDF</th>
                                            <th>Tanggal</th>
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
    @include('pages.berita.modal_tambah')
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    const initializeDataTable = () => {
        let url = "{{ route('berita.list') }}";
        return $("#berita-list-table").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: url,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'judul', name: 'judul' },
                { data: 'gambar', name: 'gambar', orderable: false, searchable: false },
                { data: 'deskripsi', name: 'deskripsi' },
                { data: 'PDF', name: 'PDF', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    className: "text-center"
                },
                {
                    targets: 5,
                    searchable: false,
                    orderable: false,
                    className: "text-center"
                }
            ],
        });
    };

    let table = initializeDataTable();

    $('.btn-add').click(function () {
        let action = "{{ route('berita.store') }}";
        $('#form-tambah').attr('action', action);
        $('#modalTitle').html('Tambah Berita');
        $('button#save').html('Tambah');
        $('#modalTambah').modal('show');
    });

    $('#berita-list-table').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        let action = "{{ route('berita.update', ':id') }}".replace(':id', id);
        let url = "{{ route('berita.edit', ':id') }}".replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                if (res.status) {
                    $('#form-tambah').attr('action', action);
                    $('#judul').val(res.data.judul);
                    $('#deskripsi').val(res.data.deskripsi);
                    $('#tanggal').val(res.data.tanggal);
                    $('#modalTitle').html('Edit Berita');
                    $('button#save').html('Simpan');
                    $('#modalTambah').modal('show');
                } else {
                    Swal.fire("Gagal!", res.message, "error");
                }
            },
            error: function (xhr) {
                Swal.fire("Gagal!", xhr.responseJSON.message, "error");
            }
        });
    });

    $('#berita-list-table').on('click', '.btn-delete', function () {
        Swal.fire({
            title: "Anda yakin hapus berita?",
            text: "Berita yang dihapus tidak dapat dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data("id");
                let url = "{{ route('berita.destroy', ':id') }}".replace(":id", id);

                $.ajax({
                    url: url,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: function () {
                        Swal.fire({ title: "Mohon Tunggu", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    },
                    success: function (res) {
                        Swal.fire(res.status ? "Berhasil!" : "Gagal!", res.message, res.status ? "success" : "error");
                        if (res.status) table.ajax.reload();
                    },
                    error: function (xhr) {
                        Swal.fire("Gagal!", xhr.responseJSON.message, "error");
                    }
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
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                Swal.fire({ title: "Mohon Tunggu", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            },
            success: function (res) {
                if (res.status) {
                    $("#modalTambah").modal("hide");
                    Swal.fire("Berhasil!", res.message, "success");
                    table.ajax.reload();
                    $("#form-tambah")[0].reset();
                } else {
                    Swal.fire("Gagal!", res.message, "error");
                }
            },
            error: function (xhr) {
                Swal.fire("Gagal!", xhr.responseJSON.message, "error");
            }
        });
    });
</script>

@endpush
