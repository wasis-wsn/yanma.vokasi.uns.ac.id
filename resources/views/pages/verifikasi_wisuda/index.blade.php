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
    <div class="container-fluid content-inner mt-n5 py-0">
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
    <div class="container-fluid content-inner py-0">
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
                        @php $verifikasi = auth()->user()->verifikasiWisuda; @endphp

                        {{-- Tampilkan semua alert hanya jika ada catatan --}}
                        @if(!empty($verifikasi->catatan))

                            {{-- Jika status_id == 2 tampilkan alert hijau --}}
                            @if($verifikasi->status_id == 2)
                                <div class="alert alert-success">
                                    <h5><i class="fa fa-check-circle"></i> Status Diverifikasi</h5>
                                    <p class="mb-0">Pengajuan telah diverifikasi.</p>
                                </div>

                            {{-- Jika status_id == 3 tampilkan alert biru --}}
                            @elseif($verifikasi->status_id == 3)
                                <div class="alert alert-primary">
                                    <h5><i class="fa fa-info-circle"></i> Menunggu Informasi Selanjutnya</h5>
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
                                <tr>
                                    <td width="30%">Tanggal Terbit Ijazah</td>
                                    <td>: 
                                        @if($verifikasi->tanggal_terbit)
                                            {{ \Carbon\Carbon::parse($verifikasi->tanggal_terbit)->translatedFormat('d F Y') }}
                                        @else
                                            Belum tersedia
                                        @endif
                                    </td>
                                </tr>
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
                                @if(!empty($verifikasi->catatan))
                                <tr>
                                    <td>Catatan Staff</td>
                                    <td>: {{ $verifikasi->catatan }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        {{-- Confirmation section only for students WITH certificate serial number and eligible status --}}
                        @if(!empty($verifikasi->no_seri_ijazah) && in_array($verifikasi->status_id, ['1']) && empty($verifikasi->tanggal_proses))
                        <div class="mt-3">
                            <div class="alert alert-info border-2 shadow-sm p-4 rounded-3">
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-1"><i class="fa fa-info-circle"></i> Upload Dokumen Kehadiran Wisuda</h5>
                                    <p class="mb-0 text-muted">Gabungkan semua persyaratan menjadi <strong>1 file PDF</strong> sebelum diunggah.</p>
                                </div>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item">1. Surat Keterangan Lulus (SKL)</li>
                                    <li class="list-group-item">2. Ijazah Terakhir</li>
                                    <li class="list-group-item">3. Pas Foto 3x4 dan 4x6</li>
                                    <li class="list-group-item">4. TOEFL/TOEIC</li>
                                    <li class="list-group-item">5. KHS Terakhir</li>
                                    <li class="list-group-item">6. Bukti Pembayaran SPP Semester Terakhir (dapat diunduh dari SIAKAD)</li>
                                    <li class="list-group-item">7. KTP</li>
                                    <li class="list-group-item">8. Bukti Penyerahan Skripsi/Tugas Akhir</li>
                                    <li class="list-group-item">9. Surat Bebas Kopma</li>
                                    <li class="list-group-item">10. Surat Bebas Perpustakaan</li>
                                    <li class="list-group-item">11. Surat Bebas Laboratorium <span class="text-muted">(Jika Ada)</span></li>
                                    <li class="list-group-item">12. Surat Keterangan Perpanjangan Masa Studi <span class="text-muted">(Jika Ada)</span></li>
                                    <li class="list-group-item">13. Surat Keterangan Selang/Cuti <span class="text-muted">(Jika Ada)</span></li>
                                    <li class="list-group-item">14. E-Journal <span class="text-muted">(Jika Ada)</span></li>
                                </ul>
                                <div class="mb-3">
                                    <span class="text-danger fw-bold">Pastikan semua berkas lengkap, jika tidak lengkap pengajuan tidak akan diproses.</span>
                                </div>
                            </div>

                            {{-- Show alert if status was reset to 1 by staff --}}
                            @if($verifikasi->status_id == '1' && $verifikasi->file_validasi_uploaded === false && !empty($verifikasi->tanggal_proses))
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Status Direset:</strong> Staff telah mengubah status Anda. Anda perlu mengupload ulang file validasi untuk melanjutkan proses konfirmasi.
                            </div>
                            @endif

                            
                        </div>
                        {{-- File Upload section for students WITH certificate serial number --}}
                        @if(!empty($verifikasi->no_seri_ijazah) && in_array($verifikasi->status_id, ['1']) && empty($verifikasi->tanggal_proses))
                            {{-- Show upload form if no file uploaded yet --}}
                            @if(empty($verifikasi->file))
                            <div class="mt-3">
                                <h6>Upload Dokumen Kehadiran Wisuda</h6>
                                <p class="text-muted">Silakan Upload Dokumen Kehadiran Wisuda terlebih dahulu sebelum melakukan konfirmasi kehadiran wisuda.</p>
                                
                                <form id="form-upload-validasi" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="file_validasi" class="form-label">Dokumen Kehadiran Wisuda <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="file_validasi" name="file" 
                                               accept=".pdf" required>
                                        <div class="form-text">Format yang diizinkan: PDF (Max: 10MB)</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> Upload File
                                    </button>
                                </form>
                            </div>
                            @else
                            {{-- Show file info and confirmation buttons if file already uploaded --}}
                            <div class="mt-3">
                                <div class="alert alert-success">
                                    <i class="fa fa-check-circle"></i> File validasi telah berhasil diupload: {{ $verifikasi->file }}
                                    <br>
                                    <a href="{{ asset('storage/verifWisuda/upload/' . $verifikasi->file) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fa fa-eye"></i> Lihat File
                                    </a>
                                </div>
                            </div>
                            @endif
                        @endif
                        @elseif($verifikasi->status_id == '4')
                        <div class="alert alert-success mt-3">
                            <i class="fa fa-check-circle"></i> Silahkan menunggu hasil Verifikasi oleh Akademik Sekolah Vokasi
                        </div>
                        @elseif($verifikasi->status_id == '1' && !empty($verifikasi->tanggal_proses))
                        <div class="alert alert-warning mt-3">
                            <i class="fa fa-clock-o"></i> File konfirmasi telah diupload. Menunggu konfirmasi admin.
                        </div>
                        @elseif($verifikasi->status_id == '6')
                        <div class="alert alert-success mt-3">
                            <i class="fa fa-check-circle"></i> Data anda telah divalidasi oleh admin, silahkan konfirmasi kehadiran wisuda.
                        </div>
                        {{-- Only show confirmation buttons for status 6 if file is uploaded --}}
                        @if(!empty($verifikasi->file))
                        <div class="mt-3">
                            <h6>Konfirmasi Kehadiran Wisuda</h6>
                            <p class="text-muted">Apakah Anda bersedia mengikuti wisuda pada periode ini?</p>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Perhatian:</strong> Pilihan ini akan menentukan status kehadiran wisuda Anda.
                            </div>

                            <div class="d-flex gap-3">
                                <button type="button" class="btn btn-success" id="btn-setuju"
                                    data-id="{{ encodeId($verifikasi->id) }}">
                                    <i class="fa fa-check"></i> Bersedia
                                </button>
                                <button type="button" class="btn btn-danger" id="btn-tidak-setuju"
                                    data-id="{{ encodeId($verifikasi->id) }}">
                                    <i class="fa fa-times"></i> Tidak Bersedia
                                </button>
                            </div>
                        </div>
                        @else
                        {{-- Show upload form for status 6 students without file --}}
                        <div class="mt-3">
                            <h6>Upload File Validasi</h6>
                            <p class="text-muted">Silakan upload file validasi terlebih dahulu sebelum melakukan konfirmasi kehadiran wisuda.</p>
                            
                            <form id="form-upload-validasi" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="file_validasi" class="form-label">File Validasi <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="file_validasi" name="file" 
                                           accept=".pdf" required>
                                    <div class="form-text">Format yang diizinkan: PDF (Max: 10MB)</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> Upload File
                                </button>
                            </form>
                        </div>
                        @endif
                        @elseif($verifikasi->status_id == '3')
                        <div class="alert alert-danger mt-3">
                            <i class="fa fa-times-circle"></i> Anda belum dapat mengikuti wisuda pada periode ini <strong>(kuota telah penuh)</strong>, Silahkan konfirmasi ulang pada periode selnjutnya.
                        </div>
                        @elseif($verifikasi->status_id == '5')
                        <div class="alert alert-danger mt-3">
                            <i class="fa fa-times-circle"></i> Anda tidak bersedia hadir pada wisuda periode ini, Silahkan konfirmasi ulang kehadiran pada periode selanjutnya.
                        </div>
                        @elseif($verifikasi->status_id == '7')
                        <div class="alert alert-info mt-3">
                            <i class="fa fa-check-circle"></i> Mohon untuk menunggu proses untuk validasi dokumen.
                        </div>
                        @elseif($verifikasi->status_id == '2')
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Alur Penyelesaian Verifikasi Wisuda</h4>
                                <br>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="iq-timeline0 m-0 d-flex align-items-center justify-content-between position-relative">
                                <ul class="list-inline p-0 m-0">
                                    <li >
                                        <div class="timeline-dots timeline-dot1 border-primary text-primary mx-auto"></div>
                                        <h6 class="mt-3 mb-1"> Data calon wisudawan telah diverifikasi</h6>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-warning text-warning"></div>
                                        <h6 class="float-left mb-1">Sinkronisasi Data di Website Wisuda</h6>
                                        <div class="d-inline-block w-100">
                                            <p>Calon wisudawan melakukan sinkronisasi data SIAKAD melalui <a href="https://wisuda.uns.ac.id" target="_blank">wisuda.uns.ac.id</a></p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-success text-success"></div>
                                        <h6 class="float-left mb-1">Melihat PIN Akses Wisuda</h6>
                                        <div class="d-inline-block w-100">
                                            <p>Calon wisudawan melihat kode/PIN akses wisuda, tertera pada baris ke-6 dari bawah pada halaman tersebut.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-info text-info"></div>
                                        <h6 class="float-left mb-1">Login dan Cetak Draft Ijazah</h6>
                                        <div class="d-inline-block w-100">
                                            <p>Gunakan PIN tersebut untuk login ke sistem wisuda. Setelah masuk, calon wisudawan dapat mencetak draft ijazah untuk dicek kembali sebelum pencetakan final.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="timeline-dots timeline-dot1 border-success text-success"></div>
                                        <h6 class="float-left mb-1">Penyerahan Berkas ke Akademik Pusat</h6>
                                        <div class="d-inline-block w-100">
                                            <p>Calon wisudawan harus menyerahkan dokumen persyaratan wisuda ke Akademik Pusat. Ketentuan dan daftar berkas dapat dilihat secara lengkap di <a href="https://wisuda.uns.ac.id" target="_blank">wisuda.uns.ac.id</a>.</p>
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
                        @endif
                        @else
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Informasi</h5>
                            <p>Anda belum terdaftar untuk wisuda. Data akan muncul setelah staff melakukan
                                import data.</p>
                        </div>
                        @endif
                        @endcan

                        @cannot('mahasiswa')
                        {{-- Existing Verifikasi Wisuda Table --}}
                        <div class="d-flex justify-content-start pb-4">
                            <button type="button" class="btn btn-info mx-2" id="btn-import">Import Data</button>
                            <button type="button" class="btn btn-secondary mx-2" id="btn-bulk-action" disabled>
                                <i class="fa fa-tasks"></i> Multi Proses
                            </button>
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
                                        <th><input type="checkbox" id="select-all" class="form-check-input"></th>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>No Seri Ijazah</th>
                                        <th>Tanggal Terbit</th>
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
</div>

{{-- Daftar Wisudawan Card --}}
@can('staff')
<div class="container-fluid content-inner mt-n5 py-0" style="margin-top: -2rem;">
    <div class="col-sm-12 mb-4">
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
                        <button type="button" class="btn btn-secondary mx-2" id="btn-bulk-action-wisudawan" disabled>
                            <i class="fa fa-tasks"></i> Multi Proses
                        </button>
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
                                <th><input type="checkbox" id="select-all-wisudawan" class="form-check-input"></th>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>No Seri Ijazah</th>
                                <th>Tanggal Terbit</th>
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

<!-- Bulk Process Modal for Verifikasi -->
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
                        <select name="status_id" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="2">Sudah Terverifikasi</option>
                            <option value="3">Tidak Terverifikasi</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Periode Wisuda</label>
                        <input type="month" name="periode_wisuda" class="form-control">
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

<!-- Bulk Process Modal for Wisudawan -->
<div class="modal fade" id="modalBulkProcessWisudawan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Wisudawan Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-bulk-process-wisudawan">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status_id" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="2">Terverifikasi</option>
                            <option value="3">Tidak Terverifikasi</option>
                        </select>
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

<!-- Add Export Wisudawan Modal -->
<div class="modal fade" id="modalExportWisudawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data Wisudawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-export-wisudawan" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->tahun }}" {{ $tahun->tahun == date('Y') ? 'selected' : '' }}>
                                {{ $tahun->tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="type" value="wisudawan">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Data wisudawan akan dihapus dari database setelah export berhasil.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-download"></i> Export & Hapus Data
                    </button>
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

<!-- Add Edit Periode Modal -->
<div class="modal fade" id="modalEditPeriode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Periode Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-edit-periode">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bulan</label>
                        <input type="text" id="nama_bulan" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Wisuda</label>
                        <input type="date" id="tanggal_wisuda" name="tanggal_wisuda" class="form-control">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                        <label class="for-check-label" for="is_active">
                            Jadikan periode aktif
                        </label>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Perubahan tanggal wisuda akan:
                        <p>Merubah status semua Calon Wisudawan menjadi "Belum Diproses"</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
            'uploadValidasi' => route('verifikasiWisuda.store'),
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
            'bulkProcess' => route('verifikasiWisuda.bulkProcess'),
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
