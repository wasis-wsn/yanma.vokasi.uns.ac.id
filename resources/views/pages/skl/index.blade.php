@extends('_template.master')

@section('title', 'SKL')

@push('css')
{{-- @can('staff') --}}
    <style>
        button.unselect{
            background-color: #f1f1f1;
            color: black;
            border: 1px solid #dee2e6!important;
            border-top-left-radius: .25rem!important;
            border-top-right-radius: .25rem!important;
            border-bottom-left-radius: -.75rem!important;
            border-bottom-right-radius: -.75rem!important;
            text-align: center;
            vertical-align: middle;
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
                @can('mahasiswa')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Layanan Surat Keterangan Lulus</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>
                                {!! $layanan->keterangan !!}
                            </p>
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary" id="toggleAlur" data-bs-toggle="collapse" data-bs-target="#alurAjuan" aria-expanded="false" aria-controls="alurAjuan">
                                    <i class="fas fa-eye" id="toggleIcon"></i> Lihat Alur Ajuan Layanan
                                </button>
                            </div>
                            <div class="collapse" id="alurAjuan">
                                <h6 class="mb-2">Alur Ajuan Layanan Surat Keterangan Lulus</h6>
                                <div class="iq-timeline0 m-0 d-flex align-items-center justify-content-between position-relative">
                                    <ul class="list-inline p-0 m-0">
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-info text-info"></div>
                                            <h6 class="float-left my-2">Mengganti foto pada SIAKAD sesuai dengan referensi</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Silahkan lihat referensi <a href="https://drive.google.com/drive/folders/1nYGEcmztMPrIXn9RopVWnbvJwx5U0WaK?usp=sharing" target="_blank" rel="noopener noreferrer">disini</a></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-success text-success"></div>
                                            <h6 class="float-left mb-1">Mengajukan SKL di SIAKAD UNS</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Silahkan mengajukan SKL melalui <a href="https://siakad.uns.ac.id/" target="_blank" rel="noopener noreferrer">SIAKAD</a></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-danger text-danger"></div>
                                            <h6 class="float-left mb-1">Mengajukan SKL di sistem ini</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Silahkan tambah ajuan dengan cara klik "Ajukan Surat Keterangan Lulus" pada tombol dibawah dan upload file yang diperlukan</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="timeline-dots timeline-dot1 border-primary text-primary"></div>
                                            <h6 class="float-left mb-1">Cek Status Ajuan</h6>
                                            <div class="d-inline-block w-100">
                                                <p>Cek Status Ajuan Anda pada tabel dibawah</p>
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
                        </div>
                    </div>
                @endcan

                <div class="card">
                    @can('mahasiswa')
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Pengajuan Surat Keterangan Lulus</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (auth()->user()->skl == null)
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">Ajukan Surat Keterangan Lulus</button>
                                    @include('pages.skl.modal_tambah')
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
                    @endcan
                    @cannot('mahasiswa')
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Layanan Surat Keterangan Lulus</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @canany(['staff','dekanat','subkoor','adminprodi'])
                                <div class="d-flex justify-content-start pb-4 px-4">
                                    <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                                    <!-- Add bulk action button -->
                                    @canany(['staff', 'fo'])
                                    <button type="button" class="btn btn-secondary mx-2" id="btn-bulk-action" disabled>
                                        <i class="fa fa-tasks"></i> Multi Proses
                                    </button>
                                    @endcanany
                                </div>
                            @endcanany
                            <div class="d-flex justify-content-end pb-4">
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="prodiDropdown" data-bs-toggle="dropdown" data-prodi="all" aria-expanded="false">Prodi</button>
                                    <ul class="dropdown-menu" aria-labelledby="prodiDropdown">
                                        <li><a class="dropdown-item prodi-menu" href="#" data-prodi="all">Semua</a></li>
                                        @foreach ($prodis as $prodi)
                                        <li><a class="dropdown-item prodi-menu" href="#" data-prodi="{{ $prodi->id }}">{{ $prodi->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Status</button>
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
                                            <th hidden>created_at</th>
                                            <th><input type="checkbox" id="select-all" class="form-check-input"></th>
                                            <td>No</td>
                                            <td>Nama</td>
                                            <td>NIM</td>
                                            <td>Prodi</td>
                                            <td>Tanggal Submit</td>
                                            <td>Tanggal Proses</td>
                                            <td>Status</td>
                                            <td>Nomor SKL</td>
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
        @include('modals.proses')
    @endcan
    @can('staff')
        @include('modals.proses')

        <!-- Add bulk process modal here -->
        <div class="modal fade" id="modalBulkProcess" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Proses Data Terpilih</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-bulk-process">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="status_id" id="bulk_status_id" class="form-select" required>
                                    <option value="">Pilih Status</option>
                                    @foreach ($status as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3" id="bulk-form-no-surat" hidden>
                                <label>Nomor Surat</label>
                                <input type="text" name="no_surat" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea name="catatan" rows="3" class="form-control"></textarea>
                            </div>
                            <input type="hidden" name="selected_ids">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    @cannot('mahasiswa')
        @include('modals.export')
        @include('pages.skl.modal_detail_skl')
    @endcannot

@endsection

@push('js')
    <script>
        // Toggle button text and icon
        document.getElementById('alurAjuan').addEventListener('shown.bs.collapse', function () {
            document.getElementById('toggleAlur').innerHTML = '<i class="fas fa-eye-slash" id="toggleIcon"></i> Sembunyikan Alur Ajuan';
        });

        document.getElementById('alurAjuan').addEventListener('hidden.bs.collapse', function () {
            document.getElementById('toggleAlur').innerHTML = '<i class="fas fa-eye" id="toggleIcon"></i> Lihat Alur Ajuan Layanan';
        });

        @if (session('error'))
            toastr.error("{{session('error')}}")
        @endif
    </script>

    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
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
            var prodi_table = $("#prodiDropdown").data('prodi');
            window.Laravel = {};
            window.Laravel.skl = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('skl.export'),
                'listData' => route('skl.listStaff'),
                'getData' => route('skl.show', ':id'),
                'routeProses' => route('skl.proses', ':id'),
                'bulkProcess' => route('skl.bulkProcess'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    {{-- Front Office Scripts --}}
    @can('fo')
        <script>
            let year = $("#tahunDropdown").html();
            let status_table = $("#statusDropdown").data('status');
            let prodi_table = $("#prodiDropdown").data('prodi');

            window.Laravel = {};
            window.Laravel.skl = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('skl.listFo'),
                'getData' => route('skl.show', ':id'),
                'routeProses' => route('skl.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/skl/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    {{-- Dekanat Scripts --}}
    @canany(['dekanat','subkoor','adminprodi'])
        <script>
            let year = $("#tahunDropdown").html();
            let status_table = $("#statusDropdown").data('status');
            let prodi_table = $("#prodiDropdown").data('prodi');

            window.Laravel = {};
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
