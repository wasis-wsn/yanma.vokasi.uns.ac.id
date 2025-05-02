@extends('_template.master')

@section('title', 'Akreditasi')

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
                                <h4 class="card-title">Akreditasi List</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end px-4 pb-2">
                            <button class="btn-add btn btn-primary d-none d-md-inline-block">Tambah akreditasi</button>
                            <button class="btn-add btn btn-sm btn-primary d-md-none">Tambah akreditasi</button>
                        </div>
                        <div class="d-flex justify-content-end px-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="prodiDropdown" data-bs-toggle="dropdown" data-prodi="all" aria-expanded="false">Semua</button>
                                <ul class="dropdown-menu" aria-labelledby="prodiDropdown">
                                    <li><a class="dropdown-item prodi-menu" href="#" data-prodi="all">Semua</a></li>
                                    @foreach ($prodis as $st)
                                    <li><a class="dropdown-item prodi-menu" href="#" data-prodi="{{ $st->id }}">{{ $st->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="user-list-table" class="table table-striped w-100">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Prodi</th>
                                            <th>Tahun</th>
                                            <th>File</th>
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
    @include('pages.akreditasi.modal_tambah')

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    let prodi_table = $("#prodiDropdown").data('prodi');
    const initializeDataTable = (prodi) => {
        let url = "{{route('akreditasi.list')}}";
        return $("#user-list-table").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: `${url}?prodi=${prodi}`,
            columns: [
                { data: "DT_RowIndex" },
                { data: "prodi.name" },
                { data: "tahun" },
                { data: "file" },
                { data: "action" },
            ],
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    targets: 4,
                },{
                    searchable: false,
                    className: "text-center",
                    targets: 0,
                },
            ],
            order: [[1, "asc"]],
        });
    };

    let table = initializeDataTable(prodi_table);

    $().ready(function() {
        $('#prodi_id').select2({
            dropdownParent: $('#div_prodi_id'),
            theme: 'bootstrap-5',
            placeholder: "Pilih Prodi",
        });
    })

    $(".prodi-menu").click(function () {
        prodi_table = $(this).data("prodi");
        $("#prodiDropdown").html($(this).html());
        table = initializeDataTable(prodi_table);
    });

    $('.btn-add').click(function () {
        let action = "{{route('akreditasi.store')}}";
        $('#form-tambah').attr('action', action);
        $('#modalTitle').html('Tambah Akreditasi');
        $('button#save').html('Tambah');
        $('#prodi_id').val('').trigger('change');
        $('#infoCustomFile1').attr('hidden', true);
        $('#modalTambah').modal('show');
    })

    $('#show_data').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        let action = "{{route('akreditasi.update', ':id')}}";
        let url = "{{route('akreditasi.edit', ':id')}}";
        action = action.replace(':id', id);
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                if (res.status) {
                    $('#form-tambah').attr('action', action);
                    $('#prodi_id').val(res.data.prodi_id).trigger('change');
                    $('#tahun').val(res.data.tahun);
                    $('#modalTitle').html('Edit Akreditasi');
                    $('button#save').html('Simpan');
                    $('#infoCustomFile1').removeAttr('hidden');
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
            title: "Anda yakin hapus Akreditasi?",
            text: "Akreditasi yang dihapus tidak dapat dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data("id");
                let url = "{{route('akreditasi.destroy', ':id')}}"
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
        let form = this;

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
    });
</script>

@endpush