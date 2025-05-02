@extends('_template.master')

@section('title', 'Keringanan UKT')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        <div class="row">                
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Keringanan UKT</h4>
                    </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-4">
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="jenisDropdown" data-bs-toggle="dropdown" data-jenis="{{$ukt[0]->jenis}}" aria-expanded="false">{{$ukt[0]->jenis}}</button>
                                <ul class="dropdown-menu" aria-labelledby="jenisDropdown">
                                    @foreach ($ukt as $item)
                                    <li><a class="dropdown-item jenis-menu" href="#" data-jenis="{{ $item->jenis }}">{{ $item->jenis }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="container text-dark">
                            <form action="{{ route('ukt.update', encodeId($ukt[0]->id)) }}" class="form" method="post" id="form">
                                <div class="form-group">
                                    <label class="form-label">Keterangan<span class="text-danger">*</span> :</label>
                                    <textarea name="keterangan" id="keterangan">{{ $ukt[0]->keterangan }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jadwal Pengajuan Keringanan UKT oleh Mahasiswa<span class="text-danger">*</span> :</label>
                                    <input type="text" class="form-control flatpickr" name="pengajuan" id="pengajuan" value="{{ $ukt[0]->pengajuan }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jadwal Verifikasi Keringanan UKT oleh Fakultas<span class="text-danger">*</span> :</label>
                                    <input type="text" class="form-control flatpickr" name="verif_fakultas" id="verif_fakultas" value="{{ $ukt[0]->verif_fakultas }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jadwal Verifikasi Keringanan UKT oleh Universitas<span class="text-danger">*</span> :</label>
                                    <input type="text" class="form-control flatpickr" name="verif_univ" id="verif_univ" value="{{ $ukt[0]->verif_univ }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Persyaratan<span class="text-danger">*</span> :</label>
                                    <textarea name="persyaratan" id="persyaratan">{{ $ukt[0]->persyaratan }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary float-end">Perbarui</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            $('#keterangan').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['height', ['height']]
                ]
            });
            $('#persyaratan').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['height', ['height']]
                ]
            });
        });

        $(".jenis-menu").click(function () {
            let jenis = $(this).data("jenis");
            $("#jenisDropdown").html($(this).html());
            let action = "{{route('ukt.update', ':id')}}";
            let url = `{{route('ukt.show')}}?jenis=${jenis}`;
            $.ajax({
                url: url,
                type: "GET",
                success: function (res) {
                    if (res.status) {
                        $('#keterangan').summernote('code', res.data.keterangan);
                        $('#pengajuan').val(res.data.pengajuan);
                        $('#verif_fakultas').val(res.data.verif_fakultas);
                        $('#verif_univ').val(res.data.verif_univ);
                        $('#persyaratan').summernote('code', res.data.persyaratan);
                        action = action.replace(':id', res.data.id);
                        $('#form').attr('action', action);
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

        $(".flatpickr").flatpickr({
            mode: "range",
            dateFormat: "d-M-Y",
        });

        $('#form').submit(function(e) {
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
        })
    </script>
@endpush