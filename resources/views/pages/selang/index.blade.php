@extends('_template.master')

@section('title', 'Selang')

@push('css')
    <style>
        #detail-alasan, #detail-catatan {
            word-wrap: break-word;
            white-space: normal;
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

    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Selang/Cuti</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <h5>
                                Jadwal Ajuan =
                                {{\Carbon\Carbon::parse($selang->open_datetime)->translatedFormat('d F Y H:i:s')}} WIB -
                                {{\Carbon\Carbon::parse($selang->close_datetime)->translatedFormat('d F Y H:i:s')}} WIB
                            </h5>
                            <p>
                                {!! $selang->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Catatan :
                                <ul class="text-dark">
                                    <li>
                                        Pengajuan diluar jadwal ajuan harus mendatangi Akademik Sekolah Vokasi
                                    </li>
                                    <li>
                                        Mahasiswa mengajukan Selang / Cuti terlebih dahulu di Siakad
                                    </li>
                                    <li>
                                        Surat Pengantar yang sudah ditanda tangani Wakil Dekan Akademik, Riset dan
                                        Kemahasiswaan Sekolah Vokasi WAJIB diambil di Front Office Sekolah Vokasi
                                    </li>
                                    <li>
                                        Surat Pengantar yang sudah diambil, WAJIB diserahkan ke Akademik UNS (Pusat) oleh mahasiswa
                                    </li>
                                </ul>
                            </p> --}}
                            @if (count($templates) > 0)
                            <p class="text-dark">
                                Template File:
                                <ul class="text-dark">
                                    @foreach ($templates as $item)
                                        <li>
                                            {{$item->template}}
                                            (<a href="{{asset('storage/template/'.$item->file)}}">download</a>)
                                        </li>
                                    @endforeach
                                </ul>
                            </p>
                            @endif
                            @if ($tanggal >= $selang->open_datetime && $tanggal <= $selang->close_datetime)
                                <div class="d-flex justify-content-end pb-4 px-4">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                                </div>
                                @include('pages.selang.modal_tambah')
                            @endif
                            @include('pages.selang.modal_edit')
                        @endcan
                        @canany(['staff', 'dekanat','subkoor'])
                            <div class="d-flex justify-content-start pb-4">
                                @can('staff')
                                    <button type="button" class="btn btn-warning mx-2" data-bs-toggle="modal" data-bs-target="#modalJadwal">Ubah Jadwal</button>
                                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                                @endcan
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.export_semester')
                            @can('staff')
                                @include('pages.selang.modal_tambah')
                                @include('pages.selang.modal_ubah_jadwal')
                            @endcan
                        @endcanany
                        @canany(['fo','staff','adminprodi'])
                            @include('modals.proses')
                        @endcanany
                        @cannot('mahasiswa')
                            <div class="d-flex justify-content-end pb-4">
                                @can('staff')
                                <button id="btn-proses-massal" class="btn btn-primary">Multi Proses</button>
                                @endcan
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="prodiDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Prodi</button>
                                    <ul class="dropdown-menu" aria-labelledby="prodiDropdown">
                                        <li><a class="dropdown-item prodi-menu" href="#" data-status="all">Semua</a></li>
                                        @foreach ($prodis as $prodi)
                                        <li><a class="dropdown-item prodi-menu" href="#" data-status="{{ $prodi->id }}">{{ $prodi->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
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
                        @endcannot
                        @include('pages.selang.modal_detail')

                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('mahasiswa')
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <!-- <th>Antrian</th> -->
                                            <th>Catatan</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Tanggal Ambil</th>
                                            {{-- <th>Semester</th> --}}
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['staff', 'dekanat','subkoor'])
                                            <th hidden>created_at</th>
                                            @can('staff')
                                            <th></th>
                                            @endcan
                                            <th>No</th>
                                            <th>Tanggal Submit</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>No Surat</th>
                                            <th>Semester</th>
                                            <th>Status</th>
                                            <!-- <th>Antrian</th> -->
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['fo','adminprodi'])
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>No Surat</th>
                                            @cannot('adminprodi')
                                                <th>Aksi</th>
                                            @endcannot
                                            <!-- <th>Antrian</th> -->
                                            <th>Tanggal Ambil</th>
                                            <th>Catatan</th>
                                        @endcan
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
<style>
    .queue-info {
        white-space: nowrap;
        text-align: center;
    }
</style>
@endsection

@push('js')
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

    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('selang.listMahasiswa'),
                'revisi' => route('selang.revisi', ':id'),
                'getData' => route('selang.show', ':id'),
                'deleteData' => route('selang.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/selang/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('fo')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi') || "all";
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('selang.listFo'),
                'update' => route('selang.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/selang/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('adminprodi')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi') || "all";
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('selang.listAdminProdi'),
                'update' => route('selang.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/selang/adminprodi.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi') || "all";

            $(() => {
                $('#mahasiswa').select2({
                    dropdownParent: $('#div_mahasiswa'),
                    theme: 'bootstrap-5',
                    placeholder: "Pilih Mahasiswa",
                    ajax: {
                        delay: 400,
                        url: "{{route('get_mhs')}}",
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
                                        text: item.nim + ' - ' + item.name + ' - ' + item.prodis.name,
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
                            return "Data Tidak Ditemukan. Pastikan Mahasiswa telah melakukan registrasi";
                        },
                        inputTooShort: function () {
                            return "Input minimal 3 huruf untuk memilih Mahasiswa";
                        },
                    },
                });
            })
        </script>

        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('selang.export'),
                'listData' => route('selang.listStaff'),
                'getData' => route('selang.show', ':id'),
                'routeProses' => route('selang.proses', ':id'),
                // Anda dapat menambahkan lebih banyak URL di sini sesuai kebutuhan
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/selang/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi') || "all";
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('selang.export'),
                'listData' => route('selang.listDekanat'),
                'getData' => route('selang.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/selang/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush
