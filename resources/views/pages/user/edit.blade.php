@extends('_template.master')

@section('title', 'Profil')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
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
            <form action="{{route('user.update', encodeId($user->id))}}" method="POST">
                @csrf
                @method('PUT')
            <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Profil</h4>
                                </div>
                            </div>
                            <div class="card-body text-dark">
                                <div class="form-group">
                                    <div class="profile-img-edit position-relative">
                                        <img class="profile-pic rounded avatar-100" src="{{@$user->foto ?? asset('back/assets/images/avatars/01.png')}}" alt="profile-pic">
                                    </div>
                                </div>
                                @can('staff')
                                <div class="form-group">
                                    <label class="form-label">Role:</label>
                                    <select name="role" class="selectpicker form-control @error('role') is-invalid @enderror" data-style="py-0" @disabled(auth()->user()->role != '2')>
                                        @foreach ($roles as $role)
                                            <option value="{{$role->id}}" @selected(old('role', $user->role) == $role->id)>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{$message}}</div>
                                    @enderror
                                </div>
                                @endcan
                                <div class="form-group role-3" hidden>
                                    <label for="pangkat" class="form-label">Pangkat/Gol/Ruang</label>
                                    <input type="text" class="form-control" id="pangkat" name="pangkat" value="{{old('pangkat', $user->pangkat)}}" placeholder="Pangkat/Gol/Ruang">
                                </div>
                                <div class="form-group role-3" hidden>
                                    <label for="jabatan" class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{old('jabatan', $user->jabatan)}}" placeholder="Jabatan">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Informasi User</h4>
                                </div>
                            </div>
                            <div class="card-body text-dark">
                                <div class="new-user-info">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="fname">Nama Lengkap<span class="text-danger">*</span>:</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="fname" name="name" value="{{old('name', $user->name)}}" placeholder="Nama Lengkap" @readonly($user->role == '5')>
                                            @error('name')
                                                <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="lname">NIM/NIP<span class="text-danger">*</span>:</label>
                                            <input type="text" class="form-control @error('nim') is-invalid @enderror" id="lname" name="nim" value="{{old('nim', $user->nim)}}" placeholder="NIM/NIP" @readonly($user->role == '5')>
                                            @error('nim')
                                                <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="add1">Email<span class="text-danger">*</span>:</label>
                                            <input type="email" autocomplete="username" class="form-control @error('email') is-invalid @enderror" id="add1" name="email" value="{{old('email', $user->email)}}" placeholder="Email" @readonly(auth()->user()->role != '2' || $user->role == '1')>
                                            @error('email')
                                                <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-12 role-1 role-7" id="div_prodi" hidden>
                                            <label class="form-label">Program Studi<span class="text-danger">*</span>:</label>
                                            <select name="prodi" id="prodi" class="selectpicker form-control @error('prodi') is-invalid @enderror">
                                                @foreach ($prodis as $prodi)
                                                    <option value="{{$prodi->id}}" @selected(old('prodi', $user->prodi) == $prodi->id)>{{$prodi->name}}</option>
                                                @endforeach
                                                <!--<option value="{{$user->prodi}}" selected>{{$user->prodis->name ?? '-'}}</option>-->
                                            </select>
                                            @error('prodi')
                                                <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-12 role-1 role-7" hidden>
                                            <label class="form-label" for="no_wa">Nomor Whatsapp<span class="text-danger">*</span>:</label>
                                            <input type="text" class="form-control @error('no_wa') is-invalid @enderror" id="no_wa" name="no_wa" value="{{old('no_wa', $user->no_wa)}}" placeholder="">
                                            @error('no_wa')
                                                <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-12 role-5" id="div_pembina" hidden>
                                            <label class="form-label">Pembina:</label>
                                            @can('staff')
                                                <select name="pembina" id="pembina" class="selectpicker form-control @error('pembina') is-invalid @enderror">
                                                    <option value="{{$user->pembina_id ?? '1'}}" selected>{{$user->pembina->name ?? '-'}}</option>
                                                </select>
                                            @endcan
                                            @can('ormawa')
                                                <input type="text" class="form-control @error('pembina') is-invalid @enderror" value="{{$user->pembina->name}}" @readonly($user->role == '5')>
                                                <input type="hidden" class="form-control @error('pembina') is-invalid @enderror" id="pembina" name="pembina" value="{{$user->pembina_id}}" @readonly($user->role == '5')>
                                            @endcan
                                            @error('pembina')
                                                <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @cannot('mahasiswa')
                                        <hr>
                                        <h5>Security</h5>
                                        <small class="text-danger mb-5">Jangan input apapun jika tidak ingin ganti Password!</small>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="form-label" for="pass">Password:</label>
                                                <input type="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" name="password" id="pass" placeholder="Password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="form-label" for="rpass">Repeat Password:</label>
                                                <input type="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" id="rpass" placeholder="Repeat Password ">
                                            </div>
                                        </div>
                                    @endcannot
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    $().ready(() => {
        let role = {{ $user->role }};
        $(`.role-${role}`).removeAttr('hidden');

        $('#prodi').select2({
            dropdownParent: $('#div_prodi'),
            theme: 'bootstrap-5',
            placeholder: "Pilih Prodi",
            // ajax: {
            //     delay: 400,
            //     url: "{{route('get_prodi')}}",
            //     type: "GET",
            //     dataType: "json",
            //     data: function (params) {
            //         return {
            //             q: $.trim(params.term),
            //         };
            //     },
            //     processResults: function(data) {
            //         return {
            //             results: $.map(data, function(item) {
            //                 return {
            //                     text: item.name,
            //                     id: item.id
            //                 }
            //             })
            //         };
            //     },
            //     escapeMarkup: function(markup) {
            //         return markup;
            //     }
            // },
            // minimumInputLength: 3,
            // language: {
            //     noResults: function() {
            //         return "Prodi Tidak Ditemukan. Pastikan data prodi sudah ada";
            //     },
            //     inputTooShort: function () {
            //         return "Input minimal 3 huruf untuk memilih Prodi";
            //     },
            // },
        });
    })
</script>

@can('staff')
    <script>
        $().ready(() => {
            $('#pembina').select2({
                dropdownParent: $('#div_pembina'),
                theme: 'bootstrap-5',
                placeholder: "Pilih Pembina",
                ajax: {
                    delay: 400,
                    url: "{{route('get_dosen')}}",
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
                        return "Pembina Tidak Ditemukan. Pastikan data pembina sudah ada";
                    },
                    inputTooShort: function () {
                        return "Input minimal 3 huruf untuk memilih Pembina";
                    },
                },
            });
        })

        const role_class = ['role-3', 'role-1', 'role-5', 'role-7'];
        $('select[name="role"]').change(function() {
            role_class.forEach(role => {
                $(`.${role}`).attr('hidden', true);
            });

            $('#pangkat').val('');
            $('#jabatan').val('');
            $('#prodi').val('{{$user->prodi}}').trigger("change");
            $('#pembina').val('{{$user->pembina_id}}').trigger("change");

            let role = $(this).val();
            $(`.role-${role}`).removeAttr('hidden');
        })
    </script>
@endcan

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>

@if (session('error'))
    <script>
        toastr.error("{{session('error')}}")
    </script>
@endif
@if (session('success'))
    <script>
        toastr.success("{{session('success')}}")
    </script>
@endif
@endpush