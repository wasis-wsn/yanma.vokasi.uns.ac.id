@extends('_template.master')

@section('title', 'Pembina')

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
                                <h4 class="card-title">Pembina List</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end px-4">
                            <button class="btn-add btn btn-primary d-none d-md-inline-block">Tambah pembina</button>
                            <button class="btn-add btn btn-sm btn-primary d-md-none">Tambah pembina</button>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="user-list-table" class="table table-striped">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>NIP/NIK</th>
                                            <th>NIDN</th>
                                            <th>Unit</th>
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
    @include('pages.pembina.modal_tambah')

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    var table = $('#user-list-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: "{{route('pembina.list')}}",
        columns: [
            { data: "DT_RowIndex" },
            { data: "name" },
            { data: "nip" },
            { data: "nidn" },
            { data: "prodi.name" },
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
                className: "text-center",
                targets: 0,
            },
        ],
        order: [[1, 'asc']]
    });

    $('.btn-add').click(function () {
        let action = "{{route('pembina.store')}}";
        $('#form-tambah').attr('action', action);
        $('#form-tambah input').val('');
        $('#form-tambah select').val('');
        $('#modalTitle').html('Tambah Pembina');
        $('button#save').html('Tambah');
        $('#modalTambah').modal('show');
    })

    $('#show_data').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        let action = "{{route('pembina.update', ':id')}}";
        let url = "{{route('pembina.edit', ':id')}}";
        action = action.replace(':id', id);
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                if (res.status) {
                    $('#form-tambah').attr('action', action);
                    $('#name').val(res.data.name);
                    $('#nip').val(res.data.nip);
                    $('#nidn').val(res.data.nidn);
                    let $oldUnit = $("<option selected='selected'></option>")
                        .val(res.data.unit_id)
                        .text(res.data.prodi.name);
                    $("#unit_id").append($oldUnit).trigger("change");
                    $('#modalTitle').html('Edit Pembina');
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
    });

    $('#unit_id').select2({
        dropdownParent: $('#div_unit_id'),
        theme: 'bootstrap-5',
        placeholder: "Pilih Unit",
        ajax: {
            delay: 200,
            url: "{{route('get_prodi')}}",
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
                return "Data Tidak Ditemukan.";
            },
            inputTooShort: function () {
                return "Input minimal 3 huruf untuk memilih Unit";
            },
        },
    });

    $("#show_data").on("click", ".btn-delete", function () {
        Swal.fire({
            title: "Anda yakin hapus pembina?",
            text: "Pembina yang dihapus tidak dapat dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data("id");
                let url = "{{route('pembina.destroy', ':id')}}"
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