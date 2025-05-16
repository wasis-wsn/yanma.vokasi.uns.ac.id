@extends('_template.master')

@section('title', 'Surat Keterangan')

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
                            <h4 class="card-title">Surat Keterangan / Pengantar</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <p>
                                {!! $layanan->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Surat Keterangan / Pengantar adalah surat yang menerangkan bahwa Anda berstatus aktif sebagai mahasiswa dan digunakan untuk keperluan <b class="text-danger">bersifat Umum</b>,  diantaranya adalah untuk keperluan: 
                                <ul>
                                    <li class="text-dark">pengantar ke kantor kepolisian untuk mengurus kehilangan</li>
                                    <li class="text-dark">syarat pembukaan rekening bank</li>
                                    <li class="text-dark">syarat pendaftaran suatu lomba/kegiatan</li>
                                    <li class="text-dark">syarat pendaftaran beasiswa</li>
                                    <li class="text-dark">syarat mengurus bpjs</li>
                                    <li class="text-dark">syarat permohonan layanan kesehatan</li>
                                    <li class="text-dark">dan lain-lain …..</li>
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
                            <div class="d-flex justify-content-end pb-4 px-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                            </div>
                            @include('pages.surat_keterangan.modal_tambah')
                            @include('pages.surat_keterangan.modal_edit')
                        @endcan
                        @canany(['staff','dekanat','subkoor'])
                            <div class="d-flex justify-content-start pb-4">
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.proses')
                            @include('modals.export_semester')
                        @endcanany
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
                        @include('pages.surat_keterangan.modal_detail')
                        
                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('mahasiswa')
                                            <th>No</th>
                                            <th>Keperluan Surat</th>
                                            <th>Tanggal Submit</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Tanggal Proses</th>
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['staff','dekanat','subkoor','adminprodi'])
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Keperluan Surat</th>
                                            <th>No Surat</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        @if (session('error'))
            toastr.error("{{session('error')}}")
        @endif
    </script>

    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('suket.listMahasiswa'),
                'revisi' => route('suket.revisi', ':id'),
                'getData' => route('suket.show', ':id'),
                'deleteData' => route('suket.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_keterangan/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('suket.export'),
                'listData' => route('suket.listStaff'),
                'getData' => route('suket.show', ':id'),
                'routeProses' => route('suket.proses', ':id'),
                // Anda dapat menambahkan lebih banyak URL di sini sesuai kebutuhan
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_keterangan/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('suket.export'),
                'listData' => route('suket.listDekanat'),
                'getData' => route('suket.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_keterangan/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
    @canany('adminprodi')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('suket.export'),
                'listData' => route('suket.listAdminProdi'),
                'getData' => route('suket.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_keterangan/adminprodi.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush