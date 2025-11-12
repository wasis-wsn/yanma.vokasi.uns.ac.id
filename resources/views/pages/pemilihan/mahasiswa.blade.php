@extends('_template.master')

@section('title', 'Pemilu Mahasiswa')

@php
    $presmbenVote = optional($pemilihanPresmben?->votes?->first());
    $calegVote = optional($pemilihanCaleg?->votes?->first());

    $presmbenSelectedCandidate = $presmbenVote && $pemilihanPresmben
        ? $pemilihanPresmben->candidates->firstWhere('id', $presmbenVote->candidate_id)
        : null;
    $calegSelectedCandidate = $calegVote && $pemilihanCaleg
        ? $pemilihanCaleg->candidates->firstWhere('id', $calegVote->candidate_id)
        : null;
    $presmbenEligible = $eligibility['presmben'] ?? true;
    $calegEligible = $eligibility['caleg'] ?? false;

    $stepperConfig = [
        'presmben' => [
            'enabled' => (bool) $pemilihanPresmben,
            'is_open' => $pemilihanPresmben?->votingWindowIsOpen() ?? false,
            'vote_url' => $pemilihanPresmben ? route('pemilihan.vote', $pemilihanPresmben) : null,
            'user_vote' => $presmbenVote?->candidate_id,
            'eligible' => $presmbenEligible,
        ],
        'caleg' => [
            'enabled' => (bool) $pemilihanCaleg,
            'is_open' => $pemilihanCaleg?->votingWindowIsOpen() ?? false,
            'vote_url' => $pemilihanCaleg ? route('pemilihan.vote', $pemilihanCaleg) : null,
            'user_vote' => $calegVote?->candidate_id,
            'optional' => true,
            'eligible' => $calegEligible,
        ],
    ];
@endphp

@push('css')
<style>
    .pemilu-empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }

    .pemilihan-stepper-card {
        border: none;
        box-shadow: 0 10px 30px rgba(15, 37, 64, 0.08);
    }

    .pemilihan-stepper-header {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .pemilihan-stepper-card .stepper-item {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        background-color: #fff;
        display: flex;
        gap: 0.9rem;
        align-items: center;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .pemilihan-stepper-card .stepper-item .stepper-number {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        background-color: #e9ecef;
        color: #1f2a37;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .pemilihan-stepper-card .stepper-item.active {
        border-color: #0d6efd;
        background-color: #f3f7ff;
    }

    .pemilihan-stepper-card .stepper-item.active .stepper-number {
        background-color: #0d6efd;
        color: #fff;
    }

    .pemilihan-stepper-card .stepper-item.completed {
        border-color: #198754;
        background-color: #f2fbf5;
    }

    .pemilihan-stepper-card .stepper-item.completed .stepper-number {
        background-color: #198754;
        color: #fff;
    }

    .pemilihan-stepper-card .step-pane {
        display: none;
    }

    .pemilihan-stepper-card .step-pane.active {
        display: block;
    }

    .pemilihan-option {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        padding: 1.25rem;
        cursor: pointer;
        position: relative;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        min-height: 170px;
    }

    .pemilihan-option:hover {
        border-color: #0d6efd;
    }

    .pemilihan-option-photo {
        width: 100%;
        max-height: 180px;
        overflow: hidden;
        border-radius: 0.75rem;
        margin-bottom: 0.85rem;
        background-color: #f5f7fb;
    }

    .pemilihan-option-photo img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .pemilihan-option.is-selected {
        border-color: #0d6efd;
        box-shadow: 0 8px 18px rgba(13, 110, 253, 0.12);
    }

    .pemilihan-option.is-disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pemilihan-option-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background-color: #198754;
        color: #fff;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 0.15rem 0.65rem;
        border-radius: 999px;
        letter-spacing: 0.03em;
    }

    .pemilihan-option-number {
        font-weight: 600;
        color: #0d6efd;
        font-size: 0.95rem;
    }

    .pemilihan-step-note {
        background: #f8f9fb;
        border-radius: 0.85rem;
        padding: 1rem 1.25rem;
    }

    .pemilihan-summary-item {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .pemilihan-summary-item:last-child {
        margin-bottom: 0;
    }

    .pemilihan-summary-title {
        font-weight: 600;
        font-size: 1rem;
    }

    .pemilihan-stepper-footer {
        border-top: 1px solid #f1f3f5;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="position-relative">
    @include('_template.navbar')
    <div class="iq-navbar-header" style="height: 160px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1>Pemilu Mahasiswa</h1>
                            <p>Pilih calon terbaikmu untuk Presmben maupun Caleg.</p>
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

<div class="conatiner-fluid content-inner mt-n5 py-0">
    @if(!$pemilihanPresmben && !$pemilihanCaleg)
        <div class="card">
            <div class="pemilu-empty-state">
                <i class="fa-solid fa-person-booth fa-3x mb-3"></i>
                <h4>Belum ada pemilihan aktif</h4>
                <p>Pantau terus halaman ini ketika periode pemilihan dimulai.</p>
            </div>
        </div>
    @else
        <div class="card pemilihan-stepper-card" id="pemilihanStepper" data-config='@json($stepperConfig)'>
            <div class="card-body">
                <div class="pemilihan-stepper-header mb-4">
                    <div class="stepper-item active" data-step="1">
                        <span class="stepper-number">1</span>
                        <div>
                            <p class="text-muted small mb-1">Langkah 1</p>
                            <h6 class="mb-0">Presiden BEM</h6>
                            <span class="text-muted small">Tentukan pilihan utama kamu.</span>
                        </div>
                    </div>
                    <div class="stepper-item" data-step="2">
                        <span class="stepper-number">2</span>
                        <div>
                            <p class="text-muted small mb-1">Langkah 2</p>
                            <h6 class="mb-0">Legislatif DEMA</h6>
                            <span class="text-muted small">Lewati jika bukan dapilmu.</span>
                        </div>
                    </div>
                    <div class="stepper-item" data-step="3">
                        <span class="stepper-number">3</span>
                        <div>
                            <p class="text-muted small mb-1">Langkah 3</p>
                            <h6 class="mb-0">Tinjau & Selesai</h6>
                            <span class="text-muted small">Pastikan semua jawaban benar.</span>
                        </div>
                    </div>
                </div>

                <div class="step-pane active" data-step-pane="1">
                    <div class="mb-4">
                        <h5 class="mb-1">Pemilihan Presiden BEM</h5>
                        <p class="text-muted mb-0">
                            Pilih satu pasangan calon yang paling kamu percaya untuk memimpin BEM Sekolah Vokasi.
                        </p>
                    </div>

                    @if($pemilihanPresmben && $pemilihanPresmben->candidates->isNotEmpty())
                        <div class="row g-3">
                            @foreach($pemilihanPresmben->candidates as $candidate)
                                @php
                                    $isSelected = $presmbenVote?->candidate_id === $candidate->id;
                                    $ketuaSummary = collect([
                                        $candidate->ketua_nama,
                                        $candidate->ketua_prodi,
                                        $candidate->ketua_angkatan ? 'Angkatan ' . $candidate->ketua_angkatan : null,
                                    ])->filter()->implode(' • ');
                                    $wakilSummary = collect([
                                        $candidate->wakil_nama,
                                        $candidate->wakil_prodi,
                                        $candidate->wakil_angkatan ? 'Angkatan ' . $candidate->wakil_angkatan : null,
                                    ])->filter()->implode(' • ');
                                    $candidateSummary = collect([
                                        $ketuaSummary ? 'Ketua: ' . $ketuaSummary : null,
                                        $wakilSummary ? 'Wakil: ' . $wakilSummary : null,
                                        $candidate->visi ? 'Visi: ' . $candidate->visi : null,
                                        $candidate->misi ? 'Misi: ' . $candidate->misi : null,
                                        $candidate->deskripsi,
                                    ])->filter()->implode(' | ');
                                @endphp
                                <div class="col-md-6">
                                    <label class="pemilihan-option {{ $isSelected ? 'is-selected' : '' }} {{ !$stepperConfig['presmben']['is_open'] ? 'is-disabled' : '' }}"
                                        data-pemilihan="presmben"
                                        data-candidate-id="{{ $candidate->id }}"
                                        data-candidate-name="{{ $candidate->name }}"
                                        data-candidate-nomor="{{ $candidate->nomor_urut }}"
                                        data-candidate-description="{{ \Illuminate\Support\Str::limit(strip_tags($candidateSummary), 160) }}"
                                    >
                                        <input type="radio" name="presmben_candidate" value="{{ $candidate->id }}" class="d-none" @checked($isSelected)>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="pemilihan-option-number">No. {{ $candidate->nomor_urut }}</span>
                                            <span class="badge bg-light text-dark">{{ $candidate->total_votes ?? 0 }} suara</span>
                                        </div>
                                        @if($candidate->photo_url)
                                            <div class="pemilihan-option-photo">
                                                <img src="{{ $candidate->photo_url }}" alt="Foto {{ $candidate->name }}">
                                            </div>
                                        @endif
                                        <div class="pemilihan-option-title fw-semibold mb-1">{{ $candidate->name }}</div>
                                        <div class="text-muted small mb-1"><strong>Ketua:</strong> {{ $ketuaSummary ?: '-' }}</div>
                                        <div class="text-muted small mb-1"><strong>Wakil:</strong> {{ $wakilSummary ?: '-' }}</div>
                                        @if($candidate->visi)
                                            <div class="text-muted small mb-1">Visi: {{ $candidate->visi }}</div>
                                        @endif
                                        @if($candidate->misi)
                                            <div class="text-muted small mb-1">Misi: {{ $candidate->misi }}</div>
                                        @endif
                                        @if($candidate->deskripsi)
                                            <div class="text-muted small">{{ $candidate->deskripsi }}</div>
                                        @endif
                                        @if($isSelected)
                                            <span class="pemilihan-option-badge">Pilihanmu</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @elseif($pemilihanPresmben)
                        <div class="alert alert-warning mb-0">
                            Belum ada calon yang terdaftar dalam pemilihan ini.
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Jadwal pemilihan Presiden BEM belum diumumkan untuk saat ini.
                        </div>
                    @endif

                    <div class="pemilihan-step-note mt-4">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i>
                            <span class="text-muted small">
                                Suara hanya dapat dikirim satu kali. Pastikan pilihanmu sudah sesuai sebelum melanjutkan ke tahap berikutnya.
                            </span>
                        </div>
                        @if($presmbenSelectedCandidate)
                            <div class="text-success small mt-2">
                                Kamu sudah memilih kandidat nomor {{ $presmbenSelectedCandidate->nomor_urut ?? '-' }} ({{ $presmbenSelectedCandidate->name ?? 'tidak diketahui' }}).
                                Perubahan tidak diperbolehkan.
                            </div>
                        @elseif(!$stepperConfig['presmben']['is_open'])
                            <div class="text-warning small mt-2">
                                Periode pemilihan belum dibuka atau sudah ditutup.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="step-pane" data-step-pane="2">
                    <div class="mb-4">
                        <h5 class="mb-1">Pemilihan Legislatif DEMA</h5>
                        <p class="text-muted mb-0">
                            Pilih calon legislatif sesuai dapilmu. Jika tidak ada dapil yang dibuka untukmu, lewati langkah ini.
                        </p>
                    </div>

                    @if(!$calegEligible)
                        <div class="alert alert-info mb-0">
                            Prodi kamu tidak tercantum dalam dapil legislatif yang sedang dibuka. Kamu tidak perlu memilih caleg.
                        </div>
                    @elseif($pemilihanCaleg && $pemilihanCaleg->candidates->isNotEmpty())
                        <div class="row g-3">
                            @foreach($pemilihanCaleg->candidates as $candidate)
                                @php
                                    $isSelected = $calegVote?->candidate_id === $candidate->id;
                                    $ketuaSummary = collect([
                                        $candidate->ketua_nama,
                                        $candidate->ketua_prodi,
                                        $candidate->ketua_angkatan ? 'Angkatan ' . $candidate->ketua_angkatan : null,
                                    ])->filter()->implode(' • ');
                                    $wakilSummary = collect([
                                        $candidate->wakil_nama,
                                        $candidate->wakil_prodi,
                                        $candidate->wakil_angkatan ? 'Angkatan ' . $candidate->wakil_angkatan : null,
                                    ])->filter()->implode(' • ');
                                    $candidateSummary = collect([
                                        $ketuaSummary ? 'Ketua: ' . $ketuaSummary : null,
                                        $wakilSummary ? 'Wakil: ' . $wakilSummary : null,
                                        $candidate->visi ? 'Visi: ' . $candidate->visi : null,
                                        $candidate->misi ? 'Misi: ' . $candidate->misi : null,
                                        $candidate->deskripsi,
                                    ])->filter()->implode(' | ');
                                @endphp
                                <div class="col-md-6">
                                    <label class="pemilihan-option {{ $isSelected ? 'is-selected' : '' }} {{ !$stepperConfig['caleg']['is_open'] ? 'is-disabled' : '' }}"
                                        data-pemilihan="caleg"
                                        data-candidate-id="{{ $candidate->id }}"
                                        data-candidate-name="{{ $candidate->name }}"
                                        data-candidate-nomor="{{ $candidate->nomor_urut }}"
                                        data-candidate-description="{{ \Illuminate\Support\Str::limit(strip_tags($candidateSummary), 160) }}"
                                    >
                                        <input type="radio" name="caleg_candidate" value="{{ $candidate->id }}" class="d-none" @checked($isSelected)>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="pemilihan-option-number">No. {{ $candidate->nomor_urut }}</span>
                                            <span class="badge bg-light text-dark">{{ $candidate->total_votes ?? 0 }} suara</span>
                                        </div>
                                        @if($candidate->photo_url)
                                            <div class="pemilihan-option-photo">
                                                <img src="{{ $candidate->photo_url }}" alt="Foto {{ $candidate->name }}">
                                            </div>
                                        @endif
                                        <div class="pemilihan-option-title fw-semibold mb-1">{{ $candidate->name }}</div>
                                        <div class="text-muted small mb-1"><strong>Ketua:</strong> {{ $ketuaSummary ?: '-' }}</div>
                                        <div class="text-muted small mb-1"><strong>Wakil:</strong> {{ $wakilSummary ?: '-' }}</div>
                                        @if($candidate->visi)
                                            <div class="text-muted small mb-1">Visi: {{ $candidate->visi }}</div>
                                        @endif
                                        @if($candidate->misi)
                                            <div class="text-muted small mb-1">Misi: {{ $candidate->misi }}</div>
                                        @endif
                                        @if($candidate->deskripsi)
                                            <div class="text-muted small">{{ $candidate->deskripsi }}</div>
                                        @endif
                                        @if($isSelected)
                                            <span class="pemilihan-option-badge">Pilihanmu</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @elseif($pemilihanCaleg)
                        <div class="alert alert-warning mb-0">
                            Belum ada calon legislatif yang aktif di dapilmu.
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Tidak ada pemilihan legislatif yang sedang berlangsung.
                        </div>
                    @endif

                    <div class="pemilihan-step-note mt-4">
                        <div class="text-muted small">
                            @if(!$calegEligible)
                                Kamu otomatis melewati langkah ini karena prodi kamu bukan bagian dari dapil aktif.
                            @else
                                Jika kamu bukan bagian dari dapil yang dibuka atau belum ada dapil yang ditugaskan, klik tombol
                                <strong>"Saya Bukan Dapil Ini"</strong> untuk melewati langkah ini.
                            @endif
                        </div>
                        @if($calegSelectedCandidate)
                            <div class="text-success small mt-2">
                                Kamu sudah memilih kandidat legislatif nomor {{ $calegSelectedCandidate->nomor_urut ?? '-' }} ({{ $calegSelectedCandidate->name ?? 'tidak diketahui' }}).
                                Suara tidak dapat diganti.
                            </div>
                        @elseif(!$calegEligible)
                            <div class="text-info small mt-2">
                                Lanjutkan ke langkah berikutnya setelah memastikan pilihan Presiden BEM.
                            </div>
                        @elseif(!$stepperConfig['caleg']['is_open'] && $pemilihanCaleg)
                            <div class="text-warning small mt-2">
                                Periode pemilihan legislatif belum dibuka atau telah ditutup.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="step-pane" data-step-pane="3">
                    <div class="mb-4">
                        <h5 class="mb-1">Tinjau Pilihanmu</h5>
                        <p class="text-muted mb-0">
                            Pastikan data sudah sesuai sebelum mengirimkan suara. Setelah dikirim, kamu tidak bisa mengubah pilihan.
                        </p>
                    </div>
                    <div class="pemilihan-summary">
                        <div class="pemilihan-summary-item" data-summary-type="presmben">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">Presiden BEM</p>
                                    <div class="pemilihan-summary-title" data-summary-name="presmben">
                                        @if($presmbenSelectedCandidate)
                                            No. {{ $presmbenSelectedCandidate->nomor_urut }} - {{ $presmbenSelectedCandidate->name }}
                                        @else
                                            Belum dipilih
                                        @endif
                                    </div>
                                    <div class="text-muted small" data-summary-detail="presmben">
                                        @if($presmbenSelectedCandidate)
                                            Suara kamu sudah tercatat.
                                        @else
                                            Silakan memilih terlebih dahulu.
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-link btn-sm px-0 stepper-edit"
                                    data-target-step="1" data-summary-edit="presmben"
                                    @if($presmbenVote || !$stepperConfig['presmben']['is_open']) disabled @endif>
                                    Ubah
                                </button>
                            </div>
                        </div>
                        <div class="pemilihan-summary-item" data-summary-type="caleg">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">Legislatif DEMA</p>
                                    <div class="pemilihan-summary-title" data-summary-name="caleg">
                                        @if($calegSelectedCandidate)
                                            No. {{ $calegSelectedCandidate->nomor_urut }} - {{ $calegSelectedCandidate->name }}
                                        @elseif(!$calegEligible)
                                            Tidak diwajibkan
                                        @else
                                            Belum dipilih
                                        @endif
                                    </div>
                                    <div class="text-muted small" data-summary-detail="caleg">
                                        @if($calegSelectedCandidate)
                                            Suara kamu sudah tercatat.
                                        @elseif(!$calegEligible)
                                            Prodi kamu tidak tercantum dalam dapil aktif.
                                        @else
                                            Langkah ini dapat dilewati jika bukan dapilmu.
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-link btn-sm px-0 stepper-edit"
                                    data-target-step="2" data-summary-edit="caleg"
                                    @if($calegVote || !$stepperConfig['caleg']['is_open'] || !$calegEligible) disabled @endif>
                                    Ubah
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-4 mb-0" data-summary-note>
                        Kirim suara hanya ketika kamu sudah yakin. Sistem akan mengirim suara untuk tiap pemilihan yang belum tercatat.
                    </div>
                </div>

                <div class="pemilihan-stepper-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <button type="button" class="btn btn-link text-muted px-0 stepper-prev" disabled>Langkah Sebelumnya</button>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3 stepper-skip d-none" data-action="skip-caleg">
                            Saya Bukan Dapil Ini
                        </button>
                        <button type="button" class="btn btn-primary px-4 stepper-next" data-action="next">
                            Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js')
<script src="{{ asset('custom/js/dashboard/pemilihan.js') }}?q{{Str::random(5)}}"></script>
@endpush
