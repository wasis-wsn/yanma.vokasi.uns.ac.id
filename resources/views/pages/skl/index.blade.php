@extends('_template.master')

@section('title', 'SKL')

@push('css')
{{-- @can('staff') --}}
    <style>
        button.unselect{
            background-color: #f1f1f1;
            color: black;
            border: 1px solid #dee2e6!important;
            /* border-right: 1px solid #dee2e6!important;
            border-left: 1px solid #dee2e6!important;
            border-bottom: 1px solid rgba(0, 0, 0, 0) !important; */
            border-top-left-radius: .25rem!important;
            border-top-right-radius: .25rem!important;
            border-bottom-left-radius: -.75rem!important;
            border-bottom-right-radius: -.75rem!important;
            text-align: center;
            vertical-align: middle;
            /* padding: .5rem 1.5rem; */
            width: 200px;
        }

        button.selected{
            background-color: #3a57e8;
            color: #fff;
            border-color: #3a57e8;
            border-top-left-radius: .25rem!important;
            border-top-right-radius: .25rem!important;
            border-bottom-left-radius: -.75rem!important;
            border-bottom-right-radius: -.75rem!important;
            text-align: center;
            vertical-align: middle;
            /* padding: .5rem 1.5rem; */
            width: 200px;
        }

        button.btn-ta{
            width: 300px;
        }
    </style>
{{-- @endcan --}}
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
                @cannot('staff')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                @can('mahasiswa')
                                    <h4 class="card-title">Layanan Surat Keterangan Lulus</h4>
                                @endcan
                                @canany(['dekanat','subkoor','fo'])
                                    <h4 class="card-title">Pengajuan TTD TA</h4>
                                @endcanany
                            </div>
                        </div>
                        <div class="card-body">
                            @can('mahasiswa')
                                <p>
                                    {!! $layanan->keterangan !!}
                                </p>
                                <h6 class="mb-2">Alur Ajuan Layanan Surat Keterangan Lulus</h6>
                                <div class="iq-timeline0 m-0 d-flex align-items-center justify-content-between position-relative">
                                    <ul class="list-inline p-0 m-0">
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-primary text-primary"></div>
                                            <h6 class="float-left mb-1">Mengajukan Tanda Tangan Lembar Pengesahan Tugas Akhir</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Mahasiswa mengumpulkan berkas Lembar Pengesahan TA yang sudah di TTD Kaprodi dan Dosen Pembimbing ke Front Office SV UNS</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-success text-success"></div>
                                            <h6 class="float-left mb-1">Mengajukan SKL di Siakad UNS</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Silahkan mengajukan SKL melalui <a href="https://siakad.uns.ac.id/" target="_blank" rel="noopener noreferrer">Siakad</a></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-danger text-danger"></div>
                                            <h6 class="float-left mb-1">Mengajukan SKL di sistem ini</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Silahkan tambah ajuan dengan cara klik "Pengajuan SKL" pada tombol dibawah, kemudian klik "Ajukan Surat Keterangan Lulus" dan upload file yang diperlukan</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-primary text-primary"></div>
                                            <h6 class="float-left mb-1">Cek Status Ajuan</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Cek Status Ajuan Anda dengan cara klik "Pengajuan SKL" pada tombol dibawah,</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-warning text-warning"></div>
                                            <h6 class="float-left mb-1">Status Ajuan Selesai</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Silahkan mengambil SKL di Front Office Sekolah Vokasi pada hari Senin-Jum'at pukul 08.00 - 15.30 WIB</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
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
                            @endcan
                            @can('fo')
                                <div class="d-flex justify-content-end pb-4 px-4">
                                    <button type="button" class="btn btn-primary mx-2" id="tambahTA">Tambah Ajuan</button>
                                </div>
                            @endcan
                            @canany(['dekanat','subkoor','fo'])
                                <div class="d-flex justify-content-end pb-4">
                                    <div class="dropdown mx-2">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="status_ta" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Semua</button>
                                        <ul class="dropdown-menu" aria-labelledby="status_ta">
                                            <li><a class="dropdown-item status-ta" href="#" data-status="all">Semua</a></li>
                                            @foreach ($status_ta as $st)
                                            <li><a class="dropdown-item status-ta" href="#" data-status="{{ $st->id }}">{{ $st->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="dropdown mx-2">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahun_ta" data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                        <ul class="dropdown-menu" aria-labelledby="tahun_ta">
                                            @foreach ($tahuns as $tahun)
                                            <li><a class="dropdown-item tahun-ta" href="#" data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="ttdTA-datatable" class="table table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Nim</th>
                                                <th>Status</th>
                                                <th>Tanggal Submit</th>
                                                <th>Tanggal Ambil</th>
                                                @can('fo')
                                                <th>Aksi</th>
                                                @endcan
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="show_data_ta">
                                        </tbody>
                                    </table>
                                </div>
                            @endcanany
                        </div>
                    </div>
                @endcannot
                <div class="card">
                    @can('mahasiswa')
                        <div class="card-header">
                            <div class="border-bottom">
                                <button type="button" class="selected btn-skl btn-sm btn-ta mt-1" data-pengajuan="pengajuanTTDTA">Pengajuan Lembar Pengesahan TA
                                </button>
                                <button type="button" class="unselect btn-skl btn-sm mt-1" data-pengajuan="pengajuanSKL">Pengajuan SKL
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="section-skl" id="pengajuanTTDTA">
                                @if (auth()->user()->pengajuanTTDTA == null)
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-success" id="tambahTA" data-id="{{auth()->user()->id}}">Saya Sudah Menyerahkan Lembar Pengesahan TA</button>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="30%">Tanggal Mengajukan</td>
                                                <td>: {{ \Carbon\Carbon::parse(auth()->user()->pengajuanTTDTA->created_at)->translatedFormat('d F Y H:i:s') }} WIB</td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>: 
                                                    <button type="button" class="{{auth()->user()->pengajuanTTDTA->status->color}} btn-sm mt-1" disabled>{{auth()->user()->pengajuanTTDTA->status->name}}
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Tanggal Diambil</td>
                                                <td>: 
                                                    {{ (auth()->user()->pengajuanTTDTA->tanggal_ambil) ? \Carbon\Carbon::parse(auth()->user()->pengajuanTTDTA->tanggal_ambil)->translatedFormat('d F Y H:i:s'). 'WIB' : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Catatan</td>
                                                <td>: {{auth()->user()->pengajuanTTDTA->catatan}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            <div class="section-skl" id="pengajuanSKL" hidden>
                                @if (auth()->user()->skl == null)
                                    <div class="d-flex justify-content-center">
                                        @if (auth()->user()->pengajuanTTDTA != null && (in_array(auth()->user()->pengajuanTTDTA->status_id, ['4', '5'])))
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">Ajukan Surat Keterangan Lulus</button>
                                            @include('pages.skl.modal_tambah')
                                        @else
                                            <p>Anda tidak dapat mengajukan SKL karena Lembar Pengesahan TA Anda belum di tanda tangani oleh Dekanat</p>
                                        @endif
                                    </div>
                                @else
                                    @include('pages.skl.modal_edit')
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="30%">Tanggal Mengajukan</td>
                                                <td>: {{ \Carbon\Carbon::parse(auth()->user()->skl->created_at)->translatedFormat('d F Y H:i:s') }} WIB</td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Tanggal Diproses</td>
                                                <td>: 
                                                    {{ (auth()->user()->skl->tanggal_proses) ? \Carbon\Carbon::parse(auth()->user()->skl->tanggal_proses)->translatedFormat('d F Y H:i:s'). 'WIB' : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>: 
                                                    <button type="button" class="{{auth()->user()->skl->status->color}} btn-sm mt-1" disabled>{{auth()->user()->skl->status->name}}
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Tanggal Diambil</td>
                                                <td>: 
                                                    {{ (auth()->user()->skl->tanggal_ambil) ? \Carbon\Carbon::parse(auth()->user()->skl->tanggal_ambil)->translatedFormat('d F Y H:i:s'). 'WIB' : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%">File Upload</td>
                                                <td>: 
                                                    <a href="{{ url('storage/skl/upload/'. auth()->user()->skl->lembar_revisi) }}" target="_blank" class="btn btn-sm btn-primary">Lembar Revisi</a>
                                                    <a href="{{ url('storage/skl/upload/'. auth()->user()->skl->ss_ajuan_skl) }}" target="_blank" class="btn btn-sm btn-primary">SS SKL Siakad</a>
                                                    @if (auth()->user()->skl->status_id == '2')
                                                        <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="{{ encodeId(auth()->user()->skl->id) }}">Ajukan Revisi</button>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Catatan</td>
                                                <td>: {{auth()->user()->skl->catatan}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endcan
                    @cannot('mahasiswa')
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Layanan Surat Keterangan Lulus</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @canany(['staff','dekanat','subkoor'])
                                <div class="d-flex justify-content-end pb-4 px-4">
                                    <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                                </div>
                            @endcanany
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
                                <table id="skl-datatable" class="table table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            @can('staff')
                                                <th hidden>created_at</th>
                                            @endcan
                                            <td>No</td>
                                            <td>Nama</td>
                                            <td>NIM</td>
                                            <td>Prodi</td>
                                            <td>Tanggal Submit</td>
                                            <td>Tanggal Proses</td>
                                            <td>Status</td>
                                            <td>Nomor Surat</td>
                                            <td>Aksi</td>
                                            <td>Tanggal Diambil</td>
                                            <td>Catatan</td>
                                        </tr>
                                    </thead>
                                    <tbody id="show_data_skl"></tbody>
                                </table>
                            </div>
                        </div>
                    @endcannot
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Modal --}}
    @can('fo')
        @include('pages.skl.modal_proses_ta')
        @include('pages.skl.modal_tambah_ta')
        @include('modals.proses')
    @endcan
    @can('staff')
        @include('modals.proses')
    @endcan
    @cannot('mahasiswa')
        @include('modals.export')
        @include('pages.skl.modal_detail_skl')
        @include('pages.skl.modal_detail_ta')
    @endcannot

@endsection

@push('js')
    <script>
        @if (session('error'))
            toastr.error("{{session('error')}}")
        @endif
    </script>

    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'addTA' => route('TA.store'),
                'revisi' => route('skl.revisi', ':id'),
                'getData' => route('skl.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');

            window.Laravel = {};
            window.Laravel.skl = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('skl.export'),
                'listData' => route('skl.listStaff'),
                'getData' => route('skl.show', ':id'),
                'routeProses' => route('skl.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('fo')
        <script>
            var year_ta = $("#tahun_ta").html();
            var status_table_ta = $("#status_ta").data('status');
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {};
            window.Laravel.TA = {!! json_encode([
                'listData' => route('TA.listFo'),
                'getMhs' => route('get_mhs'),
                'routeAdd' => route('TA.store'),
                'routeProses' => route('TA.proses', ':id'),
                'routeShow' => route('TA.show', ':id'),
                'deleteData' => route('TA.destroy', ':id'),
            ]) !!};
            window.Laravel.skl = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('skl.listFo'),
                'getData' => route('skl.show', ':id'),
                'routeProses' => route('skl.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @canany(['dekanat','subkoor'])
        <script>
            var year_ta = $("#tahun_ta").html();
            var status_table_ta = $("#status_ta").data('status');
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {};
            window.Laravel.TA = {!! json_encode([
                'listData' => route('TA.listDekanat'),
                'routeShow' => route('TA.show', ':id'),
            ]) !!};
            window.Laravel.skl = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('skl.export'),
                'listData' => route('skl.listDekanat'),
                'getData' => route('skl.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush