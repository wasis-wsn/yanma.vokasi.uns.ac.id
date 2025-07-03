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

    {{-- Periode Wisuda Card - Moved to top --}}
    @can('staff')
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Periode Wisuda</h4>
                            <p class="card-text">Kelola periode wisuda per tahun. Periode yang aktif akan otomatis
                                digunakan saat import data mahasiswa.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end pb-4">
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                    id="tahunPeriodeDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">{{ date('Y') }}</button>
                                <ul class="dropdown-menu" aria-labelledby="tahunPeriodeDropdown">
                                    @foreach ($tahuns as $tahun)
                                    <li><a class="dropdown-item tahun-periode-menu" href="#"
                                            data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="periode-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bulan</th>
                                        <th>Tanggal Wisuda</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- Verifikasi Wisuda Card --}}

            <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Konfirmasi Kehadiran</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                        <p>
                            {!! $layanan->keterangan !!}
                        </p>

                        @if (!is_null(auth()->user()->verifikasiWisuda))
                        @php $verifikasi = auth()->user()->verifikasiWisuda; @endphp

                        {{-- Tampilkan semua alert hanya jika ada catatan --}}
                        @if(!empty($verifikasi->catatan))

                            {{-- Jika status_id == 2 tampilkan alert hijau --}}
                            @if($verifikasi->status_id == 2)
                                <div class="alert alert-success">
                                    <h5><i class="fa fa-check-circle"></i> Status Diverifikasi</h5>
                                    <p class="mb-0">Pengajuan telah diverifikasi oleh staff.</p>
                                </div>

                            {{-- Jika status_id == 3 tampilkan alert biru --}}
                            @elseif($verifikasi->status_id == 3)
                                <div class="alert alert-primary">
                                    <h5><i class="fa fa-info-circle"></i> Menunggu Tindakan Selanjutnya</h5>
                                    <p class="mb-0">{{ $verifikasi->catatan }}</p>
                                </div>
                            @endif
                        @endif


                        {{-- Show waiting message for students without certificate serial number --}}
                        @if(empty($verifikasi->no_seri_ijazah) && $verifikasi->status_id == '1')
                        <div class="alert alert-info">
                            <h5><i class="fa fa-clock-o"></i> Menunggu Verifikasi Staff</h5>
                            <p>Data Anda sedang dalam proses verifikasi oleh staff. Mohon menunggu hingga proses
                                verifikasi selesai.</p>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">No Seri Ijazah</td>
                                    <td>: {{ $verifikasi->no_seri_ijazah ?? 'Belum tersedia' }}</td>
                                </tr>
                                {{-- <tr>
                                    <td width="30%">Kode Akses Wisuda</td>
                                    <td>: {{ $verifikasi->kode_akses ?? 'Belum tersedia' }}</td>
                                </tr> --}}
                                <tr>
                                    <td>Status Verifikasi</td>
                                    <td>:
                                        <button type="button" class="{{$verifikasi->status->color}} btn-sm mt-1"
                                            disabled>{{$verifikasi->status->name}}
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="30%">Jadwal Wisuda</td>
                                    <td>:
                                        @php
                                            $periode = $verifikasi->periode_wisuda;
                                            $tanggalWisuda = null;

                                            if ($periode) {
                                                $periodeDate = \Carbon\Carbon::createFromFormat('Y-m', $periode);
                                                $periodeData = \App\Models\PeriodeWisuda::where('tahun', $periodeDate->year)
                                                    ->where('bulan', $periodeDate->month)
                                                    ->first();

                                                if ($periodeData && $periodeData->tanggal_wisuda) {
                                                    $tanggalWisuda = \Carbon\Carbon::parse($periodeData->tanggal_wisuda)
                                                        ->translatedFormat('d F Y');
                                                }
                                            }
                                        @endphp

                                        @if($tanggalWisuda)
                                            {{ $tanggalWisuda }}
                                        @else
                                            Belum ditentukan
                                        @endif
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>Jadwal Wisuda</td>
                                    <td>: {{ $verifikasi->jadwal ?? 'Belum ditentukan' }}</td>
                                </tr> --}}
                                @if(!empty($verifikasi->catatan))
                                <tr>
                                    <td>Catatan Staff</td>
                                    <td>: {{ $verifikasi->catatan }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        {{-- Confirmation section only for students WITH certificate serial number and eligible status --}}
                        @if(!empty($verifikasi->no_seri_ijazah) && in_array($verifikasi->status_id, ['1','6']))
                        <div class="mt-3">
                            <h6>Konfirmasi Keikutsertaan Wisuda</h6>
                            <p class="text-muted">Apakah Anda bersedia mengikuti wisuda pada periode ini?</p>

                            <div class="d-flex gap-3">
                                <button type="button" class="btn btn-success" id="btn-setuju"
                                    data-id="{{ encodeId($verifikasi->id) }}">
                                    <i class="fa fa-check"></i> Ya, Saya Setuju
                                </button>
                                <button type="button" class="btn btn-danger" id="btn-tidak-setuju"
                                    data-id="{{ encodeId($verifikasi->id) }}">
                                    <i class="fa fa-times"></i> Tidak, Saya Tidak Setuju
                                </button>
                            </div>
                        </div>
                        @elseif($verifikasi->status_id == '4')
                        <div class="alert alert-success mt-3">
                            <i class="fa fa-check-circle"></i> Anda telah mengkonfirmasi untuk mengikuti wisuda.
                        </div>
                        @elseif($verifikasi->status_id == '3')
                        <div class="alert alert-danger mt-3">
                            <i class="fa fa-times-circle"></i> Anda tidak dapat mengikuti wisuda pada periode ini.
                        </div>
                        @elseif($verifikasi->status_id == '5')
                        <div class="alert alert-danger mt-3">
                            <i class="fa fa-times-circle"></i> Anda telah menolak untuk mengikuti wisuda periode ini.
                            Data Anda masih tercatat dalam sistem verifikasi wisuda.
                        </div>
                        @endif
                        @else
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Informasi</h5>
                            <p>Anda belum terdaftar untuk wisuda. Data akan muncul di sini setelah staff melakukan
                                import data verifikasi wisuda.</p>
                        </div>
                        @endif
                        @endcan

                        @cannot('mahasiswa')
                        {{-- Existing Verifikasi Wisuda Table --}}
                        <div class="d-flex justify-content-start pb-4">
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
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>No Seri Ijazah</th>
                                        <th>Periode Wisuda</th>
                                        <th>Status</th>
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

{{-- Alur Verifikasi Wisuda Card - New separate card for students --}}
@can('mahasiswa')
<div class="conatiner-fluid content-inner py-0 mt-2">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Alur Verifikasi Wisuda</h4>
                        <p class="card-text">Panduan langkah-langkah untuk menyelesaikan proses verifikasi wisuda</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="iq-timeline0 m-0 d-flex align-items-center justify-content-between position-relative">
                        <ul class="list-inline p-0 m-0">
                            <li>
                                <div class="timeline-dots timeline-dot1 border-primary text-primary"></div>
                                <h6 class="float-left mb-1">Mengajukan Surat Keterangan Lulus (SKL)</h6>
                                <div class="d-inline-block w-100">
                                    <p>Mahasiswa mengajukan SKL melalui sistem yang akan otomatis membuat data verifikasi wisuda</p>
                                </div>
                            </li>
                            <li>
                                <div class="timeline-dots timeline-dot1 border-warning text-warning"></div>
                                <h6 class="float-left mb-1">Menunggu Verifikasi Staff</h6>
                                <div class="d-inline-block w-100">
                                    <p>Staff akan memverifikasi data Anda dan menginput nomor seri ijazah serta periode wisuda</p>
                                </div>
                            </li>
                            <li>
                                <div class="timeline-dots timeline-dot1 border-success text-success"></div>
                                <h6 class="float-left mb-1">Data Terverifikasi</h6>
                                <div class="d-inline-block w-100">
                                    <p>Setelah data terverifikasi, Anda akan melihat nomor seri ijazah dan jadwal wisuda pada halaman ini</p>
                                </div>
                            </li>
                            <li>
                                <div class="timeline-dots timeline-dot1 border-info text-info"></div>
                                <h6 class="float-left mb-1">Konfirmasi Keikutsertaan</h6>
                                <div class="d-inline-block w-100">
                                    <p>Konfirmasi keikutsertaan wisuda dengan klik tombol "Ya, Saya Setuju" atau "Tidak, Saya Tidak Setuju"</p>
                                </div>
                            </li>
                            <li>
                                <div class="timeline-dots timeline-dot1 border-success text-success"></div>
                                <h6 class="float-left mb-1">Terdaftar Sebagai Wisudawan</h6>
                                <div class="d-inline-block w-100">
                                    <p>Setelah konfirmasi, Anda akan terdaftar sebagai calon wisudawan dan dapat mengurus berkas wisuda lainnya</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    @if (count($templates) > 0)
                    <div class="mt-4">
                        <h6 class="mb-2">Template File:</h6>
                        <ul class="text-dark">
                            @foreach ($templates as $item)
                                <li class="mb-1">
                                    <strong>{{$item->template}}</strong>
                                    <a href="{{asset('storage/template/'.$item->file)}}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

{{-- Daftar Wisudawan Card --}}
@can('staff')
    <div class="conatiner-fluid content-inner @can('staff') py-0 @else mt-n5 py-0 @endcan">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Daftar Wisudawan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between pb-4">
                        <div>
                            <button type="button" class="btn btn-success mx-2" id="btn-export-wisudawan">Export Data
                                Wisudawan</button>
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
                                    <th>Catatan</th>
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

{{-- Single Export Modal --}}
<div class="modal fade" id="modalExportWisudawan" tabindex="-1" aria-labelledby="modalExportWisudawanLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportWisudawanLabel">Export Data Wisudawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-export-wisudawan" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="type" value="wisudawan">
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

<!-- Modal Edit Periode -->
<div class="modal fade" id="modalEditPeriode" tabindex="-1" aria-labelledby="modalEditPeriodeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPeriodeLabel">Edit Periode Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-edit-periode" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bulan</label>
                        <input type="text" class="form-control" id="nama_bulan" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_wisuda" class="form-label">Tanggal Wisuda</label>
                        <input type="date" class="form-control" name="tanggal_wisuda" id="tanggal_wisuda">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1">
                            <label class="form-check-label" for="is_active">
                                Aktif (Akan digunakan sebagai periode wisuda default)
                            </label>
                        </div>
                        <small class="text-muted">Hanya satu periode yang bisa aktif per tahun</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
            'listData' => route('verifikasiWisuda.list'),
            'listWisudawan' => route('verifikasiWisuda.listWisudawan'),
            'listPeriode' => route('periodeWisuda.list'),
            'updatePeriode' => route('periodeWisuda.update', ':id'),
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
