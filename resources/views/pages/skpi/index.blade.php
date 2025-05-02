@extends('_template.master')

@section('title', 'SKPI')

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
                            <h4 class="card-title">Surat Keterangan Pendamping Ijazah</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
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
                            @if (is_null(auth()->user()->skpi))
                                <p>Wisuda Anda belum diverifikasi!</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td>Nama</td>
                                            <td>: {{ auth()->user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>NIM</td>
                                            <td>: {{ auth()->user()->nim }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nomor SKPI</td>
                                            <td>: {{ auth()->user()->skpi->no_skpi }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>: 
                                                <button class="btn btn-sm {{ auth()->user()->skpi->status->color }}" disabled="disabled">{{ auth()->user()->skpi->status->name }}</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Ambil</td>
                                            <td>: {{ (auth()->user()->skpi->tanggal_ambil) ? \Carbon\Carbon::parse(auth()->user()->skpi->tanggal_ambil)->translatedFormat('d F Y H:i:s').' WIB' : '' }} </td>
                                        </tr>
                                        <tr>
                                            <td>Catatan</td>
                                            <td>: {{ auth()->user()->skpi->catatan }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        @endcan
                        @cannot('mahasiswa')
                            @canany(['staff','dekanat','subkoor'])
                                <div class="d-flex justify-content-between pb-4 px-4">
                                    <button type="button" class="btn btn-warning btn-selesai-many invisible">Ajuan Selesai</button>
                                    <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                                </div>
                                @include('modals.export')
                            @endcanany
                            @include('modals.proses')
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
                                <table id="skpi-datatable" class="table table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor SKPI</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Periode Wisuda</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                            <th>Diambil Tanggal</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="show_data">
                                    </tbody>
                                </table>
                            </div>
                        @endcannot
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        @if (session('error'))
            toastr.error("{{session('error')}}")
        @endif
    </script>

    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('skpi.export'),
                'listData' => route('skpi.listStaff'),
                'getData' => route('skpi.show', ':id'),
                'routeProses' => route('skpi.update', ':id'),
                'routeProsesMany' => route('skpi.updateMany'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skpi/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @can('fo')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('skpi.listFo'),
                'getData' => route('skpi.show', ':id'),
                'routeProses' => route('skpi.update', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skpi/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('skpi.export'),
                'listData' => route('skpi.listDekanat'),
                'getData' => route('skpi.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skpi/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush