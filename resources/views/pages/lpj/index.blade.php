@extends('_template.master')

@section('title', 'LPJ')

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
                            <h4 class="card-title">Laporan Pertanggungjawaban</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @canany(['mahasiswa', 'ormawa'])
                            <p>
                                {!! $layanan->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Setiap Ajuan Surat Tugas Delegasi dan Surat Izin Kegiatan yang telah selesai, Mahasiswa atau Ormawa wajib mengupload
                                Laporan Pertanggungjawaban
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
                            @include('pages.lpj.modal_upload')
                        @endcanany
                        @canany(['staff', 'dekanat','subkoor'])
                            @can('staff')
                                @include('modals.proses')
                            @endcan
                            <div class="d-flex justify-content-end pb-4">
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">all</button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item status-menu" href="#" data-status="all">all</a></li>
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
                        @endcanany
                        
                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @canany(['mahasiswa', 'ormawa'])
                                            <th>No</th>
                                            <th>No SIK / ST</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['staff', 'dekanat','subkoor'])
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Nomor SIK / ST</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        @endcanany
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

    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'listData' => route('lpj.listMahasiswa'),
                'baseUrl' => url('/'),
                'upload' => route('lpj.upload', ':id'),
            ]) !!};
        </script>
    @elsecan('ormawa')
        <script>
            window.Laravel = {!! json_encode([
                'listData' => route('lpj.listOrmawa'),
                'baseUrl' => url('/'),
                'upload' => route('lpj.upload', ':id'),
            ]) !!};
        </script>
    @endcan
    @canany(['mahasiswa','ormawa'])
        <script src="{{ asset('custom/js/lpj/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('lpj.listStaff'),
                'routeProses' => route('lpj.proses', ':id'),
            ]) !!};
        </script>
    @endcan
    @canany(['dekanat','subkoor'])
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('lpj.listDekanat'),
                'routeProses' => route('lpj.proses', ':id'),
            ]) !!};
        </script>
    @endcanany
    @canany(['staff','dekanat','subkoor'])
        <script src="{{ asset('custom/js/lpj/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush