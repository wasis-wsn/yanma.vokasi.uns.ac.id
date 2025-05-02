@extends('_template.master')

@section('title', 'Legalisir')

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
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Legalisir</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @canany(['staff', 'dekanat', 'subkoor'])
                            <div class="d-flex justify-content-end pb-4 px-4">
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.export')
                        @endcanany
                        @can('fo')
                            <div class="d-flex justify-content-between pb-4 px-4">
                                <button type="button" class="btn btn-primary btn-add">Tambah Ajuan</button>
                            </div>
                            @include('pages.legalisir.modal_tambah')
                            {{-- @include('pages.legalisir.modal_edit') --}}
                            @include('modals.proses')
                        @endcan
                        @include('pages.legalisir.modal_detail')
                        <div class="d-flex justify-content-end pb-4">
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Semua</button>
                                <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                    <li><a class="dropdown-item status-menu" href="#" data-status="all">Semua</a></li>
                                    @foreach ($status as $st)
                                    <li><a class="dropdown-item status-menu" href="#" data-status="{{ $st->id }}">{{ $st->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahunDropdown" data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                <ul class="dropdown-menu" aria-labelledby="tahunDropdown">
                                    @foreach ($tahuns as $tahun)
                                    <li><a class="dropdown-item tahun-menu" href="#" data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="legalisir-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th>#</th>
                                        <th>Tanggal Submit</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Prodi</th>
                                        <th>Legalisir</th>
                                        <th>Jumlah</th>
                                        <th>Keperluan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                        <th>Tanggal Ambil</th>
                                        <th>Catatan</th>
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
@endsection

@push('js')
    <script>
        @if (session('error'))
            toastr.error("{{session('error')}}")
        @endif
    </script>

    @can('fo')
        {{-- <script>
            $().ready(function () {
                $('#prodi_id').select2({
                    dropdownParent: $('#div_prodi_id'),
                    theme: 'bootstrap-5',
                    placeholder: "Pilih Prodi",
                    ajax: {
                        delay: 100,
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
                            return "Prodi Tidak Ditemukan. <button type='button' class='btn btn-xs btn-danger manual'>Klik Untuk Input Prodi secara Manual</button>";
                        },
                        inputTooShort: function () {
                            return "Input minimal 3 huruf untuk menampilkan prodi";
                        },
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });

                $(document).on('click', '.manual', function() {
                    $("#prodi_id").select2("close");
                    $('#prodi_id').select2("destroy");
                    $('#prodi_id').attr('hidden', true);
                    $('#namaProdi').removeAttr('hidden');
                })
            });
        </script> --}}

        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('legalisir.listFo'),
                'store' => route('legalisir.store'),
                'update' => route('legalisir.update', ':id'),
                'getProdi' => route('get_prodi'),
                'getData' => route('legalisir.show', ':id'),
                'deleteData' => route('legalisir.destroy', ':id'),
                'routeProses' => route('legalisir.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/legalisir/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @canany(['staff','dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('legalisir.export'),
                'listData' => route('legalisir.listStaff'),
                'getData' => route('legalisir.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/legalisir/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush