@extends('_template.master')

@section('title', 'Diluar Jadwal')

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
                            <h4 class="card-title">Pembayaran UKT dan Heregistrasi Diluar Jadwal</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <h5>
                                Jadwal Ajuan = {{\Carbon\Carbon::parse($diluarJadwal->open_datetime)->translatedFormat('d F Y H:i:s')}} WIB - {{\Carbon\Carbon::parse($diluarJadwal->close_datetime)->translatedFormat('d F Y H:i:s')}} WIB
                            </h5>
                            <p>
                                {!! $diluarJadwal->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Catatan :
                                <ul class="text-dark">
                                    <li>
                                        Pengajuan diluar jadwal ajuan harus mendatangi Akademik Sekolah Vokasi
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
                            @if ($tanggal >= $diluarJadwal->open_datetime && $tanggal <= $diluarJadwal->close_datetime)
                                <div class="d-flex justify-content-end pb-4 px-4">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                                </div>
                                @include('pages.diluar_jadwal.modal_tambah')
                            @endif
                            @include('pages.diluar_jadwal.modal_edit')
                        @endcan
                        @canany(['staff', 'dekanat', 'subkoor'])
                            <div class="d-flex justify-content-start pb-4">
                                @can('staff')
                                    <button type="button" class="btn btn-warning mx-2" data-bs-toggle="modal" data-bs-target="#modalJadwal">Ubah Jadwal</button>
                                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                                @endcan
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.export_semester')
                            @can('staff')
                                @include('modals.proses')
                                @include('pages.diluar_jadwal.modal_tambah')
                                @include('pages.diluar_jadwal.modal_ubah_jadwal')
                            @endcan
                        @endcanany
                        @can('fo')
                            @include('modals.proses')
                        @endcan
                        @cannot('mahasiswa')
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
                        @endcannot
                        @include('pages.diluar_jadwal.modal_detail')
                        
                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('mahasiswa')
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Tanggal Ambil</th>
                                            {{-- <th>Semester</th>
                                            <th>Tanggal Bayar</th> --}}
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['staff', 'dekanat', 'subkoor'])
                                            <th hidden>created_at</th>
                                            <th>#</th>
                                            <th>Tanggal <br>Submit</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>No Surat</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        @endcanany
                                        @can('fo')
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>No Surat</th>
                                            <th>Tanggal Ambil</th>
                                            <th>Aksi</th>
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
                'listData' => route('diluarJadwal.listMahasiswa'),
                'revisi' => route('diluarJadwal.revisi', ':id'),
                'getData' => route('diluarJadwal.show', ':id'),
                'deleteData' => route('diluarJadwal.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/diluarJadwal/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('fo')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('diluarJadwal.listFo'),
                'update' => route('diluarJadwal.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/diluarJadwal/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');

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
                'export' => route('diluarJadwal.export'),
                'listData' => route('diluarJadwal.listStaff'),
                'getData' => route('diluarJadwal.show', ':id'),
                'routeProses' => route('diluarJadwal.proses', ':id'),
                // Anda dapat menambahkan lebih banyak URL di sini sesuai kebutuhan
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/diluarJadwal/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat', 'subkoor'])
    <script>
        var year = $("#tahunDropdown").html();
        window.Laravel = {!! json_encode([
            'baseUrl' => url('/'),
            'export' => route('diluarJadwal.export'),
            'listData' => route('diluarJadwal.listDekanat'),
            'getData' => route('diluarJadwal.show', ':id'),
        ]) !!};
    </script>
        <script src="{{ asset('custom/js/diluarJadwal/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush