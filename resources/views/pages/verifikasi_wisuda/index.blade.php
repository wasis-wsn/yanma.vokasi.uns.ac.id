@extends('_template.master')

@section('title', 'Verifikasi Wisuda')

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
                            <h4 class="card-title">Verifikasi Wisuda</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <p>
                                {!! $layanan->keterangan !!}
                            </p>
                            {{-- <h6>
                                Ajukan Verifikasi wisuda jika sudah mendapat SKL. Verifikasi wisuda akan dilakukan jika sudah mendapatkan No Seri Ijazah Nasional.
                            </h6> --}}
                            <h6>
                                Alur Wisuda :
                            </h6>
                            <div class="iq-timeline0 my-2 d-flex align-items-center justify-content-between position-relative">
                                <ul class="list-inline p-0 m-0">
                                    <li>
                                        <div class="timeline-dots timeline-dot1"></div>
                                        <h6 class="float-left mb-1">Tambah Ajuan Verifikasi Wisuda</h6>
                                        <div class="d-inline-block w-100">
                                            <p>
                                                Silahkan input berkas syarat pendaftaran wisuda dengan klik tombol "Tambah Ajuan"
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-success"></div>
                                        <h6 class="float-left mb-1">Verifikasi</h6>
                                        <div class="d-inline-block w-100">
                                            <p>
                                                Petugas verifikator akan memverifikasi ajuanmu berdasarkan waktu submit dan dilakukan
                                                sesuai jadwal pembukaan periode wisuda (jadwal ditentukan oleh Akademik UNS)
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1"></div>
                                        <h6 class="float-left mb-1">Cek Kode Akses Wisuda</h6>
                                        <ol class="p-0" >
                                            <li class="p-0">NIM yang berhasil diverifikasi otomatis terdaftar ke dalam kuota peserta wisuda</li>
                                            <li class="p-0">
                                                Kode Akses wisuda akan didistribusikan oleh SV melalui sistem ini 
                                                atau dapat di cek pada laman <a href="https://wisuda.uns.ac.id/">wisuda.uns.ac.id</a>, 
                                                pada menu sinkronisasi data
                                            </li>
                                        </ol>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-success"></div>
                                        <h6 class="float-left mb-1">Pembayaran IKA UNS</h6>
                                        <div class="d-inline-block w-100">
                                            <p>
                                                Lakukan pembayaran iuran IKA UNS ke Bank BTN atau transfer ke nomor rekening
                                                <b>0019101500004436</b>
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1"></div>
                                        <h6 class="float-left mb-1">Pendaftaran Online Wisuda</h6>
                                        <ul class="p-0" style="list-style-type: circle;">
                                            <li class="p-0">
                                                Daftar wisuda pada laman 
                                                <a href="https://wisuda.uns.ac.id/" target="_blank">wisuda.uns.ac.id</a> 
                                                dengan menggunakan Kode Akses
                                            </li>
                                            <li class="p-0">
                                                Lengkapi dan pastikan kebenaran data sebelum submit.
                                            </li>
                                            <li class="p-0">
                                                Cetak biodata form pendaftaran wisuda dan draft ijazah
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-success"></div>
                                        <h6 class="float-left mb-1">Penyerahan Berkas</h6>
                                        <div class="d-inline-block w-100">
                                            <p>
                                                Peserta wisuda yang sudah menyelesaikan dan melengkapi poin sebelumnya,
                                                wajib menyerahkan berkas ke Akademik UNS
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1"></div>
                                        <h6 class="float-left mb-1">Wisuda</h6>
                                        <div class="d-inline-block w-100">
                                            <p>
                                                Peserta wisuda melakukan wisuda
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-success"></div>
                                        <h6 class="float-left mb-1">Transkrip Akademik</h6>
                                        <div class="d-inline-block w-100">
                                            <p>
                                                Transkrip Akademik akan otomatis diproses oleh SV setelah wisuda selesai dilaksanakan
                                            </p>
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
                            @if ($ajuan)
                            <div class="d-flex justify-content-between pb-4 px-4">
                                {{-- <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAlur">Alur Wisuda</button> --}}
                                <button type="button" class="btn btn-primary" id="btn-tambah">Buat Ajuan</button>
                            </div>
                            @elseif(!$ajuan && is_null(auth()->user()->verifikasiWisuda))
                            </div>
                            <div class="d-flex justify-content-center text-dark">
                                <p>Anda tidak dapat mengajukan Verifikasi Wisuda karena Anda belum memiliki SKL</p>
                            </div>
                            @endif
                            @if (!is_null(auth()->user()->verifikasiWisuda))
                            </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Tanggal Mengajukan</td>
                                            <td>: {{ \Carbon\Carbon::parse(auth()->user()->verifikasiWisuda->created_at)->translatedFormat('d F Y H:i:s') }} WIB</td>
                                        </tr>
                                        <tr>
                                            <td width="30%">No Seri Ijazah</td>
                                            <td>: {{ auth()->user()->verifikasiWisuda->no_seri_ijazah }}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%">Kode Akses Wisuda</td>
                                            <td>: {{ auth()->user()->verifikasiWisuda->kode_akses }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status Verifikasi</td>
                                            <td>: 
                                                <button type="button" class="{{auth()->user()->verifikasiWisuda->status->color}} btn-sm mt-1" disabled>{{auth()->user()->verifikasiWisuda->status->name}}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%">Periode Wisuda</td>
                                            <td>: {{ (auth()->user()->verifikasiWisuda->periode_wisuda) ? \Carbon\Carbon::createFromFormat('Y-m', auth()->user()->verifikasiWisuda->periode_wisuda)->translatedFormat('F Y') : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Catatan</td>
                                            <td>: {{auth()->user()->verifikasiWisuda->catatan}}</td>
                                        </tr>
                                        <tr>
                                            <td>File Upload</td>
                                            <td>: 
                                                <a href="{{ url('storage/verifWisuda/upload/'.auth()->user()->verifikasiWisuda->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-file"></i> Lihat File
                                                </a>
                                                @if (auth()->user()->verifikasiWisuda->status_id == '1')
                                                <button type="button" class="btn btn-warning btn-sm" id="btn-edit" data-id="{{ encodeId(auth()->user()->verifikasiWisuda->id) }}">
                                                    <i class="fa fa-pen"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        @endcan
                        @cannot('mahasiswa')
                            <div class="d-flex justify-content-start pb-4">
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.export')
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
                                <table id="wisuda-datatable" class="table table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Tanggal Ajuan</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>No Seri Ijazah</th>
                                            <th>Periode Wisuda</th>
                                            <th>Aksi</th>
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

{{-- Modal --}}
@can('mahasiswa')
    @include('pages.verifikasi_wisuda.modal_alur')
    @include('pages.verifikasi_wisuda.modal_tambah')
@endcan
@can('staff')
    @include('pages.verifikasi_wisuda.modal_detail')
    @include('pages.verifikasi_wisuda.modal_proses')
@endcan

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
                'store' => route('verifikasiWisuda.store'),
                'editData' => route('verifikasiWisuda.update', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/verifikasiWisuda/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('verifikasiWisuda.export'),
                'listData' => route('verifikasiWisuda.list'),
                'getData' => route('verifikasiWisuda.show', ':id'),
                'routeProses' => route('verifikasiWisuda.proses', ':id'),
                'routeEdit' => route('verifikasiWisuda.update', ':id'),
                // Anda dapat menambahkan lebih banyak URL di sini sesuai kebutuhan
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/verifikasiWisuda/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('verifikasiWisuda.export'),
                'listData' => route('verifikasiWisuda.listDekanat'),
                'getData' => route('verifikasiWisuda.show', ':id'),
                // Anda dapat menambahkan lebih banyak URL di sini sesuai kebutuhan
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/verifikasiWisuda/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush