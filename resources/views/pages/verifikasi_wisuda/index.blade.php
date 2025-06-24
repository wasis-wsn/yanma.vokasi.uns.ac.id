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
            <img src="{{asset('back/assets/images/dashboard/top-header1.png')}}" alt="header"
                class="img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div> <!-- Nav Header Component End -->
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
                        
                        @if (!is_null(auth()->user()->verifikasiWisuda))
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle"></i> Konfirmasi Keikutsertaan Wisuda</h5>
                                <p>Anda telah terdaftar untuk mengikuti wisuda. Silakan konfirmasi keikutsertaan Anda.</p>
                            </div>
                            
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Data Verifikasi Wisuda Anda</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="30%">No Seri Ijazah</td>
                                                <td>: {{ auth()->user()->verifikasiWisuda->no_seri_ijazah ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Kode Akses Wisuda</td>
                                                <td>: {{ auth()->user()->verifikasiWisuda->kode_akses ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Status Verifikasi</td>
                                                <td>:
                                                    <button type="button"
                                                        class="{{auth()->user()->verifikasiWisuda->status->color}} btn-sm mt-1"
                                                        disabled>{{auth()->user()->verifikasiWisuda->status->name}}
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Periode Wisuda</td>
                                                <td>:
                                                    {{ (auth()->user()->verifikasiWisuda->periode_wisuda) ? \Carbon\Carbon::createFromFormat('Y-m', auth()->user()->verifikasiWisuda->periode_wisuda)->translatedFormat('F Y') : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jadwal Wisuda</td>
                                                <td>: {{ auth()->user()->verifikasiWisuda->jadwal ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Catatan</td>
                                                <td>: {{ auth()->user()->verifikasiWisuda->catatan ?? '-' }}</td>
                                            </tr>
                                            @if(auth()->user()->verifikasiWisuda->catatan_mahasiswa)
                                            <tr>
                                                <td>Catatan Anda</td>
                                                <td>: {{ auth()->user()->verifikasiWisuda->catatan_mahasiswa }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    
                                    @if(in_array(auth()->user()->verifikasiWisuda->status_id, ['2', '3']))
                                        <div class="mt-3">
                                            <h6>Konfirmasi Keikutsertaan Wisuda</h6>
                                            <p class="text-muted">Apakah Anda bersedia mengikuti wisuda pada periode ini?</p>
                                            
                                            <div class="d-flex gap-3">
                                                <button type="button" class="btn btn-success" id="btn-setuju" 
                                                    data-id="{{ encodeId(auth()->user()->verifikasiWisuda->id) }}">
                                                    <i class="fa fa-check"></i> Ya, Saya Setuju
                                                </button>
                                                <button type="button" class="btn btn-danger" id="btn-tidak-setuju"
                                                    data-id="{{ encodeId(auth()->user()->verifikasiWisuda->id) }}">
                                                    <i class="fa fa-times"></i> Tidak, Saya Tidak Setuju
                                                </button>
                                            </div>
                                        </div>
                                    @elseif(auth()->user()->verifikasiWisuda->status_id == '4')
                                        <div class="alert alert-success mt-3">
                                            <i class="fa fa-check-circle"></i> Anda telah mengkonfirmasi untuk mengikuti wisuda.
                                        </div>
                                    @elseif(auth()->user()->verifikasiWisuda->status_id == '5')
                                        <div class="alert alert-warning mt-3">
                                            <i class="fa fa-exclamation-triangle"></i> Anda telah menolak untuk mengikuti wisuda periode ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle"></i> Informasi</h5>
                                <p>Anda belum terdaftar untuk wisuda. Data akan muncul di sini setelah staff melakukan import data verifikasi wisuda.</p>
                            </div>
                        @endif
                        @endcan
                        
                        @cannot('mahasiswa')
                        {{-- Existing Verifikasi Wisuda Table --}}
                        <div class="d-flex justify-content-start pb-4">
                            <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            <button type="button" class="btn btn-info mx-2" id="btn-import">Import Data</button>
                        </div>
                        @include('modals.export')
                        @include('modals.import')
                        <div class="d-flex justify-content-end pb-4">
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="statusDropdown"
                                    data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Semua</button>
                                <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                    <li><a class="dropdown-item status-menu" href="#" data-status="all">Semua</a></li>
                                    @foreach ($status as $st)
                                    <li><a class="dropdown-item status-menu" href="#"
                                            data-status="{{ $st->id }}">{{ $st->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahunDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                <ul class="dropdown-menu" aria-labelledby="tahunDropdown">
                                    @foreach ($tahuns as $tahun)
                                    <li><a class="dropdown-item tahun-menu" href="#"
                                            data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
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
                                        <!-- <th>Tanggal Ajuan</th> -->
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

{{-- Separate Card for Daftar Wisudawan Table --}}
@can('staff')
<div class="conatiner-fluid content-inner py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Daftar Wisudawan</h4>
                        <p class="card-text">Kelola status wisudawan yang telah diverifikasi</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between pb-4">
                        <div>
                            <button type="button" class="btn btn-success mx-2" id="btn-export-wisudawan">Export Data Wisudawan</button>
                        </div>
                        <div class="dropdown mx-2">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                id="tahunWisudawanDropdown" data-bs-toggle="dropdown"
                                aria-expanded="false">{{ date('Y') }}</button>
                            <ul class="dropdown-menu" aria-labelledby="tahunWisudawanDropdown">
                                @foreach ($tahuns as $tahun)
                                <li><a class="dropdown-item tahun-wisudawan-menu" href="#"
                                        data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="wisudawan-datatable" class="table table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th hidden>created_at</th>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>No Seri Ijazah</th>
                                    <th>Periode Wisuda</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="show_wisudawan_data">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Export Modal for Wisudawan --}}
<div class="modal fade" id="modalExportWisudawan" tabindex="-1" aria-labelledby="modalExportWisudawanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportWisudawanLabel">Export Data Wisudawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-export-wisudawan" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tahun_wisudawan" class="form-label">Tahun</label>
                        <select class="form-select" name="tahun" id="tahun_wisudawan" required>
                            @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->tahun }}" {{ $tahun->tahun == date('Y') ? 'selected' : '' }}>
                                {{ $tahun->tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Modal --}}
@can('mahasiswa')
@include('pages.verifikasi_wisuda.modal_konfirmasi')
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
    @if(session('error'))
    toastr.error("{{session('error')}}")
    @endif
</script>

@can('mahasiswa')
<script>
    window.Laravel = {!!json_encode([
            'baseUrl' => url('/'),
            'konfirmasi' => route('verifikasiWisuda.konfirmasi', ':id'),
        ]) !!};
</script>
<script src="{{ asset('custom/js/verifikasiWisuda/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
@endcan
@can('staff')
<script>
    var year = $("#tahunDropdown").html();
    var status_table = $("#statusDropdown").data('status');
    window.Laravel = {!!json_encode([
            'baseUrl' => url('/'),
            'export' => route('verifikasiWisuda.export'),
            'import' => route('verifikasiWisuda.import'),
            'exportWisudawan' => route('verifikasiWisuda.exportWisudawan'),
            'listData' => route('verifikasiWisuda.list'),
            'listWisudawan' => route('verifikasiWisuda.listWisudawan'),
            'getData' => route('verifikasiWisuda.show', ':id'),
            'routeProses' => route('verifikasiWisuda.proses', ':id'),
            'routeEdit' => route('verifikasiWisuda.update', ':id'),
            'routeTerima' => route('verifikasiWisuda.terima', ':id'),
            'routeTolak' => route('verifikasiWisuda.tolak', ':id'),
        ]) !!};
</script>
<script src="{{ asset('custom/js/verifikasiWisuda/staff.js') }}?q{{Str::random(5)}}"></script>
@endcan
@canany(['dekanat','subkoor'])
<script>
    var year = $("#tahunDropdown").html();
    var status_table = $("#statusDropdown").data('status');
    window.Laravel = {!!json_encode([
            'baseUrl' => url('/'),
            'export' => route('verifikasiWisuda.export'),
            'listData' => route('verifikasiWisuda.listDekanat'),
            'getData' => route('verifikasiWisuda.show', ':id'),
        ]) !!};
</script>
<script src="{{ asset('custom/js/verifikasiWisuda/staff.js') }}?q{{Str::random(5)}}"></script>
@endcanany
@endpush