@extends('_template.master')

@section('title', 'Pengajuan TTD TA')

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
                @can('mahasiswa')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Pengajuan Tanda Tangan Lembar Pengesahan Tugas Akhir</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (is_object($layanan) && isset($layanan->keterangan))
                                <p>{!! $layanan->keterangan !!}</p>
                            @endif

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
                    </div>
                @endcan

                @canany(['dekanat','subkoor','fo','adminprodi','staff'])
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Data Pengajuan TTD TA</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @can('fo')
                                <div class="d-flex justify-content-end pb-4 px-4">
                                    <button type="button" class="btn btn-primary mx-2" id="tambahTA">Tambah Ajuan</button>
                                </div>
                            @endcan
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
                        </div>
                    </div>
                @endcanany
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
@can('fo')
    @include('pages.ttd-ta.modal_proses_ta')
    @include('pages.ttd-ta.modal_tambah_ta')
@endcan
@cannot('mahasiswa')
    @include('pages.ttd-ta.modal_detail_ta')
@endcannot

@endsection

@push('js')
    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'addTA' => route('TA.store'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @can('staff')
        <script>
            var year_ta = $("#tahun_ta").html();
            var status_table_ta = $("#status_ta").data('status');
            window.Laravel = {};
            window.Laravel.TA = {!! json_encode([
                'listData' => route('TA.listStaff'),
                'routeShow' => route('TA.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/ta/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @can('fo')
        <script>
            let year_ta = $("#tahun_ta").html();
            let status_table_ta = $("#status_ta").data('status');

            window.Laravel = {};
            window.Laravel.TA = {!! json_encode([
                'listData' => route('TA.listFo'),
                'getMhs' => route('get_mhs'),
                'routeAdd' => route('TA.store'),
                'routeProses' => route('TA.proses', ':id'),
                'routeShow' => route('TA.show', ':id'),
                'getData' => route('TA.show', ':id'),
                'deleteData' => route('TA.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/ta/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor','adminprodi'])
        <script>
            let year_ta = $("#tahun_ta").html();
            let status_table_ta = $("#status_ta").data('status');

            window.Laravel = {};
            window.Laravel.TA = {!! json_encode([
                'listData' => route('TA.listDekanat'),
                'routeShow' => route('TA.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/ta/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush
