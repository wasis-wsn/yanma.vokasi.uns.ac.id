@extends('_template.master')

@section('title', 'Manajemen Pemilu')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="position-relative">
    @include('_template.navbar')
    <div class="iq-navbar-header" style="height: 120px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1>Manajemen Pemilu</h1>
                            <p>Kelola jadwal pemilihan dan calon presmben/caleg.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{asset('back/assets/images/dashboard/top-header1.png')}}" alt="header" class="img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>
</div>

<div class="conatiner-fluid content-inner mt-n5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Daftar Pemilihan</h4>
                    </div>
                    <button class="btn btn-primary btn-add-pemilihan">
                        <i class="fa fa-plus"></i> Tambah Pemilihan
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pemilihan-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Pemilihan</th>
                                    <th>Jenis</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Calon</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Kelola Calon</h4>
                        <p class="mb-0 text-muted small" id="selected-pemilihan-label">Pilih pemilihan untuk melihat calon.</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-add-candidate" disabled>
                            <i class="fa fa-user-plus"></i> Tambah Calon
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="candidate-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Profil Calon</th>
                                    <th>Prodi & Dapil</th>
                                    <th>Perolehan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada pemilihan yang dipilih.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pemilihan --}}
<div class="modal fade" id="modalPemilihan" tabindex="-1" aria-labelledby="modalPemilihanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPemilihanLabel">Tambah Pemilihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-pemilihan">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="pemilihan_id" id="pemilihan_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nama Pemilihan</label>
                            <input type="text" class="form-control" name="name" id="pemilihan_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" id="pemilihan_jenis" class="form-select" required>
                                <option value="" disabled selected>Pilih jenis</option>
                                @foreach ($jenisOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mulai Voting</label>
                            <input type="datetime-local" class="form-control" name="mulai_at" id="pemilihan_mulai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Selesai Voting</label>
                            <input type="datetime-local" class="form-control" name="selesai_at" id="pemilihan_selesai">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="pemilihan_deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="pemilihan_active" name="is_active">
                                <label class="form-check-label" for="pemilihan_active">Tampilkan menu pemilu pada dashboard</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-pemilihan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Calon --}}
<div class="modal fade" id="modalCandidate" tabindex="-1" aria-labelledby="modalCandidateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCandidateLabel">Tambah Calon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-candidate" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="candidate_id" id="candidate_id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nomor Urut</label>
                            <input type="number" min="1" class="form-control" name="nomor_urut" id="candidate_nomor" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nama Pasangan / Koalisi</label>
                            <input type="text" class="form-control" name="name" id="candidate_name" required>
                        </div>

                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Data Diri Ketua</h6>
                                    <span class="badge bg-primary-subtle text-primary">Ketua</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Ketua</label>
                                        <input type="text" class="form-control" name="ketua_nama" id="ketua_nama" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Prodi Ketua</label>
                                        <select class="form-select" name="ketua_prodi" id="ketua_prodi" required>
                                            <option value="" disabled selected>Pilih prodi</option>
                                            @foreach($prodis as $prodi)
                                                <option value="{{ $prodi->name }}">{{ $prodi->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Angkatan</label>
                                        <input type="text" class="form-control" name="ketua_angkatan" id="ketua_angkatan" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Data Diri Wakil Ketua</h6>
                                    <span class="badge bg-info-subtle text-info">Wakil</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Wakil Ketua</label>
                                        <input type="text" class="form-control" name="wakil_nama" id="wakil_nama" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Prodi Wakil</label>
                                        <select class="form-select" name="wakil_prodi" id="wakil_prodi" required>
                                            <option value="" disabled selected>Pilih prodi</option>
                                            @foreach($prodis as $prodi)
                                                <option value="{{ $prodi->name }}">{{ $prodi->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Angkatan</label>
                                        <input type="text" class="form-control" name="wakil_angkatan" id="wakil_angkatan" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="candidate_dapil_group">
                            <label class="form-label d-block">Dapil (Pilih Prodi)</label>
                            <div class="border rounded p-2 dapil-checkbox-wrapper" style="max-height: 260px; overflow-y: auto;">
                                @foreach($prodis as $prodi)
                                    <div class="form-check">
                                        <input class="form-check-input dapil-prodi-checkbox" type="checkbox" value="{{ $prodi->id }}" id="dapil_prodi_{{ $prodi->id }}" name="dapil_prodi_ids[]">
                                        <label class="form-check-label" for="dapil_prodi_{{ $prodi->id }}">
                                            {{ $prodi->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text" id="dapil_helper_text">Aktif ketika pemilihan merupakan Caleg.</div>
                        </div>

                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Visi & Misi</h6>
                                    <span class="badge bg-light text-muted">Program</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Visi</label>
                                        <textarea name="visi" id="candidate_visi" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Misi</label>
                                        <textarea name="misi" id="candidate_misi" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Deskripsi Singkat</label>
                                        <textarea name="deskripsi" id="candidate_deskripsi" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Foto Calon</label>
                            <input type="file" class="form-control" name="foto" id="candidate_foto" accept="image/*">
                            <div class="form-text">Format JPG/PNG/WebP maksimal 2 MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pratinjau Foto</label>
                            <div class="border rounded p-2 text-center" id="candidate_foto_preview_wrapper">
                                <img src="" alt="Pratinjau foto calon" id="candidate_foto_preview" class="img-fluid rounded d-none" style="max-height: 200px;">
                                <span class="text-muted small d-block" id="candidate_foto_placeholder">Belum ada foto yang dipilih.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-candidate">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
<script>
    window.PemiluRoutes = {!! json_encode([
        'list' => route('pemilu.list'),
        'store' => route('pemilu.store'),
        'update' => route('pemilu.update', ':id'),
        'delete' => route('pemilu.destroy', ':id'),
        'toggle' => route('pemilu.toggle', ':id'),
        'show' => route('pemilu.show', ':id'),
        'candidateList' => route('pemilu.candidates', ':id'),
        'candidateStore' => route('pemilu.candidates.store', ':id'),
        'candidateUpdate' => route('pemilu.candidates.update', ':id'),
        'candidateDelete' => route('pemilu.candidates.destroy', ':id'),
        'candidateShow' => route('pemilu.candidates.show', ':id'),
    ]) !!};
</script>
<script src="{{ asset('custom/js/pemilihan/manage.js') }}?q={{ Str::random(5) }}"></script>
@endpush
