@extends('_template.master')

@section('title', 'Template')

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
                                <h4 class="card-title">List Template</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end px-4">
                            <button class="btn-add btn btn-primary d-none d-md-inline-block">Tambah template</button>
                            <button class="btn-add btn btn-sm btn-primary d-md-none">Tambah template</button>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="user-list-table" class="table table-striped">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Layanan</th>
                                            <th>Template</th>
                                            <th>file</th>
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
    @include('pages.template.modal_tambah')

</div>
@endsection

@push('js')
<script>
    var table = $('#user-list-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: "{{route('template.list')}}",
        columns: [
            { data: "DT_RowIndex" },
            { data: "layanan.name" },
            { data: "template" },
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
    });

    $('.btn-add').click(function () {
        let action = "{{route('template.store')}}";
        $('#form-tambah').attr('action', action);
        $('#form-tambah input').val('');
        $('#layanan_id').val('').trigger("change");
        $('#modalTitle').html('Tambah Template');
        $('button#save').html('Tambah');
        $('#modalTambah').modal('show');
    })

    $('#show_data').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        let action = "{{route('template.update', ':id')}}";
        let url = "{{route('template.edit', ':id')}}";
        action = action.replace(':id', id);
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function (res) {
                if (res.status) {
                    $('#form-tambah').attr('action', action);
                    $('#template').val(res.data.template);
                    let $oldLayanan = $("<option selected='selected'></option>")
                        .val(res.data.layanan_id)
                        .text(res.data.layanan.name);
                    $("#layanan_id").append($oldLayanan).trigger("change");
                    $('#modalTitle').html('Edit Template');
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

    $('#layanan_id').select2({
        dropdownParent: $('#div_layanan_id'),
        theme: 'bootstrap-5',
        placeholder: "Pilih Layanan",
        ajax: {
            delay: 200,
            url: "{{route('search')}}",
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
        language: {
            noResults: function() {
                return "Layanan Tidak Ditemukan.";
            },
        },
    });

    $("#show_data").on("click", ".btn-delete", function () {
        Swal.fire({
            title: "Anda yakin hapus Template?",
            text: "Template yang dihapus tidak dapat dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data("id");
                let url = "{{route('template.destroy', ':id')}}"
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