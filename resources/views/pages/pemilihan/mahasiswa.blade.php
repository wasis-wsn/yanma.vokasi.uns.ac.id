@extends('_template.master')

@section('title', 'Pemilu Mahasiswa')

@php
    $presbemVote = optional($pemilihanPresbem?->votes?->first());
    $calegVote = optional($pemilihanCaleg?->votes?->first());

    $presbemSelectedCandidate = $presbemVote && $pemilihanPresbem
        ? $pemilihanPresbem->candidates->firstWhere('id', $presbemVote->candidate_id)
        : null;
    $calegSelectedCandidate = $calegVote && $pemilihanCaleg
        ? $pemilihanCaleg->candidates->firstWhere('id', $calegVote->candidate_id)
        : null;
    $presbemEligible = $eligibility['presbem'] ?? true;
    $calegEligible = $eligibility['caleg'] ?? false;
    $hasSklRestriction = $userHasSkl ?? false;

    if ($hasSklRestriction) {
        $presbemEligible = false;
        $calegEligible = false;
    }

    $presbemIneligibleReason = !$presbemEligible && $hasSklRestriction ? 'skl' : null;
    $calegIneligibleReason = !$calegEligible
        ? ($hasSklRestriction ? 'skl' : 'dapil')
        : null;

    $stepperConfig = [
        'presbem' => [
            'enabled' => (bool) $pemilihanPresbem,
            'is_open' => $pemilihanPresbem?->votingWindowIsOpen() ?? false,
            'vote_url' => $pemilihanPresbem ? route('pemilihan.vote', $pemilihanPresbem) : null,
            'user_vote' => $presbemVote?->candidate_id,
            'eligible' => $presbemEligible,
            'ineligible_reason' => $presbemIneligibleReason,
        ],
        'caleg' => [
            'enabled' => (bool) $pemilihanCaleg,
            'is_open' => $pemilihanCaleg?->votingWindowIsOpen() ?? false,
            'vote_url' => $pemilihanCaleg ? route('pemilihan.vote', $pemilihanCaleg) : null,
            'user_vote' => $calegVote?->candidate_id,
            'eligible' => $calegEligible,
            'ineligible_reason' => $calegIneligibleReason,
        ],
    ];
    $fallbackPaslonImage = asset('paslon.png');
    // Perbaikan: Cek apakah vote benar-benar exists dengan mengecek ID atau atribut lain
    $presbemCompleted = !$pemilihanPresbem || ($presbemVote && $presbemVote->id);
    $calegCompleted = !$pemilihanCaleg || !$calegEligible || ($calegVote && $calegVote->id);
    $hasFinishedPemilihan = $presbemCompleted && $calegCompleted;
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

    .pemilihan-option-grid > .col,
    .pemilihan-option-grid > [class^="col-"] {
        display: flex;
    }

    .pemilihan-option {
        border: 1px solid #e6ebf1;
        border-radius: 1.25rem;
        padding: 1.5rem;
        cursor: pointer;
        position: relative;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        min-height: 220px;
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        gap: 1rem;
        background-color: #fff;
        box-shadow: 0 12px 30px rgba(15, 37, 64, 0.08);
        overflow: hidden;
    }

    .pemilihan-option:hover {
        border-color: #0d6efd;
        box-shadow: 0 18px 36px rgba(13, 110, 253, 0.18);
        transform: translateY(-4px);
    }

    .pemilihan-option-photo {
        width: 100%;
        height: 320px;
        overflow: hidden;
        border-radius: 1rem;
        background-color: #f5f7fb;
        position: relative;
        flex-shrink: 0;
        border: 1px solid #edf1f7;
        box-shadow: 0 6px 16px rgba(15, 37, 64, 0.08);
        margin-bottom: 1rem;
    }

    .pemilihan-option-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        display: block;
    }

    .pemilihan-option.is-selected {
        border-color: #0d6efd;
        box-shadow: 0 16px 32px rgba(13, 110, 253, 0.15);
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
        box-shadow: 0 8px 14px rgba(25, 135, 84, 0.25);
    }

    .pemilihan-option-number {
        font-weight: 600;
        color: #0d6efd;
        font-size: 0.85rem;
        background-color: rgba(13, 110, 253, 0.12);
        border-radius: 999px;
        padding: 0.25rem 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 1rem;
        left: 1rem;
    }

    .pemilihan-option-body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        position: relative;
    }

    .pemilihan-option-layout {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .pemilihan-option-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .pemilihan-option-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1f2a37;
        margin-bottom: 0.5rem;
    }

    .pemilihan-option-meta {
        font-size: 0.85rem;
        color: #5f6c75;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .pemilihan-option-meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.75rem;
        background-color: #f8f9fb;
        border-radius: 0.65rem;
        border-left: 3px solid #0d6efd;
    }

    .pemilihan-option-meta-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        color: #0d6efd;
        letter-spacing: 0.05em;
    }

    .pemilihan-option-meta-value {
        font-size: 0.875rem;
        color: #1f2a37;
        line-height: 1.4;
        font-weight: 600;
    }

    .pemilihan-option-meta-sub {
        font-size: 0.8rem;
        color: #6c757d;
        line-height: 1.3;
        display: block;
        margin-top: 0.25rem;
    }

    .pemilihan-option-footer {
        margin-top: auto;
        padding-top: 0.75rem;
        border-top: 1px dashed #e6ebf1;
        font-size: 0.8rem;
        color: #5f6c75;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
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

    /* Golput Option Styles */
    .pemilihan-option-golput {
        background: #f8f9fa;
        min-height: auto !important;
    }

    .pemilihan-option-golput:hover {
        border-color: #6c757d !important;
        background: #e9ecef;
    }

    .pemilihan-option-golput.is-selected {
        border-color: #0d6efd !important;
        box-shadow: 0 8px 18px rgba(13, 110, 253, 0.15) !important;
        background: #e7f1ff;
    }

    .pemilihan-thanks-state {
        padding: 3rem 1rem;
        text-align: center;
    }

    .pemilihan-thanks-icon {
        width: 72px;
        height: 72px;
        border-radius: 999px;
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .pemilihan-thanks-list {
        max-width: 540px;
        margin: 2rem auto 0;
        text-align: left;
    }

    .pemilihan-thanks-list li + li {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px dashed #dee2e6;
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
                            <p>Pilih calon terbaikmu untuk Presbem maupun Caleg.</p>
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
    @if(!$pemilihanPresbem && !$pemilihanCaleg)
        <div class="card">
            <div class="pemilu-empty-state">
                <i class="fa-solid fa-person-booth fa-3x mb-3"></i>
                <h4>Belum ada pemilihan aktif</h4>
                <p>Pantau terus halaman ini ketika periode pemilihan dimulai.</p>
            </div>
        </div>
    @else
        @if($hasFinishedPemilihan)
            <div class="card pemilihan-stepper-card">
                <div class="card-body">
                    <div class="pemilihan-thanks-state">
                        <div class="pemilihan-thanks-icon mb-3">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h4 class="mb-2">Terima kasih sudah mengikuti pemilihan!</h4>
                        <p class="text-muted mb-4">
                            Suaramu sudah tercatat di sistem. Nantikan pengumuman resmi hasil pemilihan dari panitia.
                        </p>
                        <ul class="list-unstyled pemilihan-thanks-list text-muted small mb-0">
                            @if($pemilihanPresbem)
                                <li>
                                    <span class="d-block text-dark fw-semibold mb-1">Presiden BEM</span>
                                    @if($presbemSelectedCandidate)
                                        No. {{ $presbemSelectedCandidate->nomor_urut }} - {{ $presbemSelectedCandidate->name }}
                                    @elseif($presbemVote)
                                        Kotak kosong dipilih.
                                    @else
                                        Suara kamu sudah tercatat.
                                    @endif
                                </li>
                            @endif
                            @if($pemilihanCaleg)
                                <li>
                                    <span class="d-block text-dark fw-semibold mb-1">Legislatif DEMA</span>
                                    @if(!$calegEligible)
                                        @if($hasSklRestriction)
                                            Kamu tidak dapat mengikuti pemilihan legislatif karena sudah memiliki SKL.
                                        @else
                                            Kamu tidak terdaftar pada dapil legislatif yang aktif saat ini.
                                        @endif
                                    @elseif($calegSelectedCandidate)
                                        No. {{ $calegSelectedCandidate->nomor_urut }} - {{ $calegSelectedCandidate->name }}
                                    @elseif($calegVote)
                                        Kotak kosong dipilih.
                                    @else
                                        Suara kamu sudah tercatat.
                                    @endif
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="card pemilihan-stepper-card" id="pemilihanStepper" data-config='@json($stepperConfig)'>
                <div class="card-body">
                    @if($hasSklRestriction)
                        <div class="alert alert-warning d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-exclamation fa-lg mt-1"></i>
                            <div>
                                <div class="fw-semibold mb-1">Kamu sudah tercatat memiliki SKL.</div>
                                <p class="mb-0">Sesuai ketentuan, pemegang SKL tidak diperkenankan mengikuti pemilihan mahasiswa.</p>
                            </div>
                        </div>
                    @endif
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

                    @if(!$presbemEligible && $hasSklRestriction)
                        <div class="alert alert-info mb-0">
                            Kamu tidak dapat mengikuti pemilihan Presiden BEM karena sudah memiliki SKL.
                        </div>
                    @elseif($pemilihanPresbem && $pemilihanPresbem->candidates->isNotEmpty())
                        <div class="row g-3 pemilihan-option-grid">
                            @foreach($pemilihanPresbem->candidates as $candidate)
                                @php
                                    $isSelected = $presbemVote?->candidate_id === $candidate->id;
                                    $ketuaNama = $candidate->ketua_nama;
                                    $ketuaProdi = $candidate->ketua_prodi;
                                    $ketuaAngkatan = $candidate->ketua_angkatan ? "'" . $candidate->ketua_angkatan : null;
                                    $ketuaSummary = collect([$ketuaNama, $ketuaProdi, $ketuaAngkatan])->filter()->implode(' • ');
                                    $wakilNama = $candidate->wakil_nama;
                                    $wakilProdi = $candidate->wakil_prodi;
                                    $wakilAngkatan = $candidate->wakil_angkatan ? "'" . $candidate->wakil_angkatan : null;
                                    $wakilSummary = collect([$wakilNama, $wakilProdi, $wakilAngkatan])->filter()->implode(' • ');
                                    $dapilName = $candidate->dapil?->name;
                                    $dapilProdis = $candidate->dapil?->prodis?->pluck('name')->filter()->implode(', ');
                                    $candidateSummary = collect([
                                        $ketuaSummary ? 'Ketua: ' . $ketuaSummary : null,
                                        $wakilSummary ? 'Wakil: ' . $wakilSummary : null,
                                        $candidate->visi ? 'Visi: ' . $candidate->visi : null,
                                        $candidate->misi ? 'Misi: ' . $candidate->misi : null,
                                        $candidate->deskripsi,
                                    ])->filter()->implode(' | ');
                                    $visiPreview = $candidate->visi ? \Illuminate\Support\Str::limit(strip_tags($candidate->visi), 120) : null;
                                    $misiPreview = $candidate->misi ? \Illuminate\Support\Str::limit(strip_tags($candidate->misi), 120) : null;
                                    $deskripsiPreview = $candidate->deskripsi ? \Illuminate\Support\Str::limit(strip_tags($candidate->deskripsi), 140) : null;
                                @endphp
                                <div class="col-12 col-md-6 col-xl-4">
                                    <label class="pemilihan-option {{ $isSelected ? 'is-selected' : '' }} {{ (!$stepperConfig['presbem']['is_open'] || !$presbemEligible) ? 'is-disabled' : '' }}"
                                        data-pemilihan="presbem"
                                        data-candidate-id="{{ $candidate->id }}"
                                        data-candidate-name="{{ $candidate->name }}"
                                        data-candidate-nomor="{{ $candidate->nomor_urut }}"
                                        data-candidate-description="{{ \Illuminate\Support\Str::limit(strip_tags($candidateSummary), 160) }}"
                                    >
                                        <input type="radio" name="presbem_candidate" value="{{ $candidate->id }}" class="d-none" @checked($isSelected)>
                                        <div class="pemilihan-option-body">
                                            <span class="pemilihan-option-number">No. {{ $candidate->nomor_urut }}</span>
                                            <div class="pemilihan-option-layout">
                                                <div class="pemilihan-option-photo">
                                                    <img
                                                        src="{{ $candidate->photo_url ?: $fallbackPaslonImage }}"
                                                        alt="Foto {{ $candidate->name }}"
                                                        onerror="this.onerror=null;this.src='{{ $fallbackPaslonImage }}';"
                                                    >
                                                </div>
                                                <div class="pemilihan-option-content">
                                                    <div class="pemilihan-option-title">{{ $candidate->name }}</div>
                                                    <div class="pemilihan-option-meta">
                                                        <div class="pemilihan-option-meta-item">
                                                            <span class="pemilihan-option-meta-label">Ketua</span>
                                                            <span class="pemilihan-option-meta-value">{{ $ketuaNama ?: '-' }}</span>
                                                            @if($ketuaProdi || $ketuaAngkatan)
                                                                <span class="pemilihan-option-meta-sub">{{ collect([$ketuaProdi, $ketuaAngkatan])->filter()->implode(' • ') }}</span>
                                                            @endif
                                                        </div>
                                                        @if($wakilNama)
                                                            <div class="pemilihan-option-meta-item">
                                                                <span class="pemilihan-option-meta-label">Wakil</span>
                                                                <span class="pemilihan-option-meta-value">{{ $wakilNama }}</span>
                                                                @if($wakilProdi || $wakilAngkatan)
                                                                    <span class="pemilihan-option-meta-sub">{{ collect([$wakilProdi, $wakilAngkatan])->filter()->implode(' • ') }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($visiPreview || $misiPreview || $deskripsiPreview)
                                                <div class="pemilihan-option-footer">
                                                    @if($visiPreview)
                                                        <div><strong>Visi:</strong> {{ $visiPreview }}</div>
                                                    @endif
                                                    @if($misiPreview)
                                                        <div><strong>Misi:</strong> {{ $misiPreview }}</div>
                                                    @endif
                                                    @if($deskripsiPreview)
                                                        <div>{{ $deskripsiPreview }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @if($isSelected)
                                            <span class="pemilihan-option-badge">Pilihanmu</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                            {{-- Opsi Kotak Kosong (hanya muncul jika kandidat = 1) - Ditampilkan di posisi terakhir --}}
                            @if($pemilihanPresbem->candidates->count() === 1)
                                <div class="col-12 col-md-6 col-xl-4">
                                    @php
                                        $isKotakKosongSelected = $presbemVote?->candidate_id === null && $presbemVote !== null;
                                    @endphp
                                    <label class="pemilihan-option pemilihan-option-golput {{ $isKotakKosongSelected ? 'is-selected' : '' }} {{ (!$stepperConfig['presbem']['is_open'] || !$presbemEligible) ? 'is-disabled' : '' }}"
                                        data-pemilihan="presbem"
                                        data-candidate-id="0"
                                        data-candidate-name="Kotak Kosong"
                                        data-candidate-nomor="0"
                                        data-candidate-description="Memilih kotak kosong"
                                        style="border: 2px dashed #6c757d;"
                                    >
                                        <input type="radio" name="presbem_candidate" value="0" class="d-none" @checked($isKotakKosongSelected)>
                                        <div class="pemilihan-option-body text-center">
                                            <div class="py-4">
                                                <i class="fa-regular fa-square fa-3x text-muted mb-3"></i>
                                                <h5 class="fw-bold text-muted mb-2">Kotak Kosong</h5>
                                                <p class="text-muted small mb-0">Saya memilih kotak kosong</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @elseif($pemilihanPresbem)
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
                        @if($presbemSelectedCandidate)
                            <div class="text-success small mt-2">
                                Kamu sudah memilih kandidat nomor {{ $presbemSelectedCandidate->nomor_urut ?? '-' }} ({{ $presbemSelectedCandidate->name ?? 'tidak diketahui' }}).
                                Perubahan tidak diperbolehkan.
                            </div>
                        @elseif(!$presbemEligible && $hasSklRestriction)
                            <div class="text-danger small mt-2">
                                Kamu sudah memiliki SKL, sehingga tidak dapat mengirim suara pada pemilihan ini.
                            </div>
                        @elseif(!$stepperConfig['presbem']['is_open'])
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
                            Pilih calon legislatif sesuai dapilmu. Jika tidak ada dapil yang dibuka untukmu, kamu akan otomatis melewati langkah ini.
                        </p>
                    </div>

                    @if(!$calegEligible)
                        <div class="alert alert-info mb-0">
                            @if($hasSklRestriction)
                                Kamu tidak dapat mengikuti pemilihan legislatif karena sudah memiliki SKL.
                            @else
                                Prodi kamu tidak tercantum dalam dapil legislatif yang sedang dibuka. Kamu tidak perlu memilih caleg.
                            @endif
                        </div>
                    @elseif($calegEligible && $pemilihanCaleg && $pemilihanCaleg->candidates->isNotEmpty())
                        <div class="row g-3 pemilihan-option-grid">
                            @foreach($pemilihanCaleg->candidates as $candidate)
                                @php
                                    $isSelected = $calegVote?->candidate_id === $candidate->id;
                                    $ketuaNama = $candidate->ketua_nama;
                                    $ketuaProdi = $candidate->ketua_prodi;
                                    $ketuaAngkatan = $candidate->ketua_angkatan ? "'" . $candidate->ketua_angkatan : null;
                                    $ketuaSummary = collect([$ketuaNama, $ketuaProdi, $ketuaAngkatan])->filter()->implode(' • ');
                                    $wakilNama = $candidate->wakil_nama;
                                    $wakilProdi = $candidate->wakil_prodi;
                                    $wakilAngkatan = $candidate->wakil_angkatan ? "'" . $candidate->wakil_angkatan : null;
                                    $wakilSummary = collect([$wakilNama, $wakilProdi, $wakilAngkatan])->filter()->implode(' • ');
                                    $candidateSummary = collect([
                                        $ketuaSummary ? 'Ketua: ' . $ketuaSummary : null,
                                        $wakilSummary ? 'Wakil: ' . $wakilSummary : null,
                                        $candidate->visi ? 'Visi: ' . $candidate->visi : null,
                                        $candidate->misi ? 'Misi: ' . $candidate->misi : null,
                                        $candidate->deskripsi,
                                    ])->filter()->implode(' | ');
                                    $visiPreview = $candidate->visi ? \Illuminate\Support\Str::limit(strip_tags($candidate->visi), 120) : null;
                                    $misiPreview = $candidate->misi ? \Illuminate\Support\Str::limit(strip_tags($candidate->misi), 120) : null;
                                    $deskripsiPreview = $candidate->deskripsi ? \Illuminate\Support\Str::limit(strip_tags($candidate->deskripsi), 140) : null;
                                @endphp
                                <div class="col-12 col-md-6 col-xl-4">
                                    <label class="pemilihan-option {{ $isSelected ? 'is-selected' : '' }} {{ (!$stepperConfig['caleg']['is_open'] || !$calegEligible) ? 'is-disabled' : '' }}"
                                        data-pemilihan="caleg"
                                        data-candidate-id="{{ $candidate->id }}"
                                        data-candidate-name="{{ $candidate->name }}"
                                        data-candidate-nomor="{{ $candidate->nomor_urut }}"
                                        data-candidate-description="{{ \Illuminate\Support\Str::limit(strip_tags($candidateSummary), 160) }}"
                                    >
                                        <input type="radio" name="caleg_candidate" value="{{ $candidate->id }}" class="d-none" @checked($isSelected)>
                                        <div class="pemilihan-option-body">
                                            <span class="pemilihan-option-number">No. {{ $candidate->nomor_urut }}</span>
                                            <div class="pemilihan-option-layout">
                                                <div class="pemilihan-option-photo">
                                                    <img
                                                        src="{{ $candidate->photo_url ?: $fallbackPaslonImage }}"
                                                        alt="Foto {{ $candidate->name }}"
                                                        onerror="this.onerror=null;this.src='{{ $fallbackPaslonImage }}';"
                                                    >
                                                </div>
                                                <div class="pemilihan-option-content">
                                                    <div class="pemilihan-option-title">{{ $candidate->name }}</div>
                                                    <div class="pemilihan-option-meta">
                                                        <div class="pemilihan-option-meta-item">
                                                            <span class="pemilihan-option-meta-label">Ketua</span>
                                                            <span class="pemilihan-option-meta-value">{{ $ketuaNama ?: '-' }}</span>
                                                            @if($ketuaProdi || $ketuaAngkatan)
                                                                <span class="pemilihan-option-meta-sub">{{ collect([$ketuaProdi, $ketuaAngkatan])->filter()->implode(' • ') }}</span>
                                                            @endif
                                                        </div>
                                                        @if($wakilNama)
                                                            <div class="pemilihan-option-meta-item">
                                                                <span class="pemilihan-option-meta-label">Wakil</span>
                                                                <span class="pemilihan-option-meta-value">{{ $wakilNama }}</span>
                                                                @if($wakilProdi || $wakilAngkatan)
                                                                    <span class="pemilihan-option-meta-sub">{{ collect([$wakilProdi, $wakilAngkatan])->filter()->implode(' • ') }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if($dapilName)
                                                            <div class="pemilihan-option-meta-item">
                                                                <span class="pemilihan-option-meta-label">Dapil</span>
                                                                <span class="pemilihan-option-meta-value">
                                                                    {{ $dapilName }}@if($dapilProdis) <span class="text-muted">• {{ $dapilProdis }}</span>@endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($visiPreview || $misiPreview || $deskripsiPreview)
                                                <div class="pemilihan-option-footer">
                                                    @if($visiPreview)
                                                        <div><strong>Visi:</strong> {{ $visiPreview }}</div>
                                                    @endif
                                                    @if($misiPreview)
                                                        <div><strong>Misi:</strong> {{ $misiPreview }}</div>
                                                    @endif
                                                    @if($deskripsiPreview)
                                                        <div>{{ $deskripsiPreview }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @if($isSelected)
                                            <span class="pemilihan-option-badge">Pilihanmu</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                            {{-- Opsi Kotak Kosong untuk caleg (hanya muncul jika kandidat = 1) - Ditampilkan di posisi terakhir --}}
                            @if($pemilihanCaleg->candidates->count() === 1)
                                <div class="col-12 col-md-6 col-xl-4">
                                    @php
                                        $isKotakKosongCalegSelected = $calegVote?->candidate_id === null && $calegVote !== null;
                                    @endphp
                                    <label class="pemilihan-option pemilihan-option-golput {{ $isKotakKosongCalegSelected ? 'is-selected' : '' }} {{ (!$stepperConfig['caleg']['is_open'] || !$calegEligible) ? 'is-disabled' : '' }}"
                                        data-pemilihan="caleg"
                                        data-candidate-id="0"
                                        data-candidate-name="Kotak Kosong"
                                        data-candidate-nomor="0"
                                        data-candidate-description="Memilih kotak kosong"
                                        style="border: 2px dashed #6c757d;"
                                    >
                                        <input type="radio" name="caleg_candidate" value="0" class="d-none" @checked($isKotakKosongCalegSelected)>
                                        <div class="pemilihan-option-body text-center">
                                            <div class="py-4">
                                                <i class="fa-regular fa-square fa-3x text-muted mb-3"></i>
                                                <h5 class="fw-bold text-muted mb-2">Kotak Kosong</h5>
                                                <p class="text-muted small mb-0">Saya memilih kotak kosong</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @elseif($calegEligible && $pemilihanCaleg)
                        <div class="alert alert-warning mb-0">
                            Belum ada calon legislatif yang aktif di dapilmu.
                        </div>
                    @elseif($calegEligible)
                        <div class="alert alert-info mb-0">
                            Tidak ada pemilihan legislatif yang sedang berlangsung.
                        </div>
                    @endif

                    <div class="pemilihan-step-note mt-4">
                        <div class="text-muted small">
                            @if(!$calegEligible)
                                Kamu otomatis melewati langkah ini karena prodi kamu bukan bagian dari dapil aktif.
                            @elseif($calegEligible && $pemilihanCaleg)
                                Pastikan kamu memilih calon legislatif yang sesuai dengan dapil yang sedang dibuka.
                            @endif
                        </div>
                        @if($calegSelectedCandidate)
                            <div class="text-success small mt-2">
                                Kamu sudah memilih kandidat legislatif nomor {{ $calegSelectedCandidate->nomor_urut ?? '-' }} ({{ $calegSelectedCandidate->name ?? 'tidak diketahui' }}).
                                Suara tidak dapat diganti.
                            </div>
                        @elseif(!$calegEligible)
                            <div class="text-info small mt-2">
                                @if($hasSklRestriction)
                                    Kamu sudah memiliki SKL, sehingga tidak dapat mengikuti pemilihan legislatif.
                                @else
                                    Prodi kamu tidak tercantum dalam dapil legislatif yang sedang dibuka.
                                @endif
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
                        <div class="pemilihan-summary-item" data-summary-type="presbem">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">Presiden BEM</p>
                                    <div class="pemilihan-summary-title" data-summary-name="presbem">
                                        @if($presbemSelectedCandidate)
                                            No. {{ $presbemSelectedCandidate->nomor_urut }} - {{ $presbemSelectedCandidate->name }}
                                        @elseif(!$presbemEligible)
                                            Tidak dapat memilih
                                        @else
                                            Belum dipilih
                                        @endif
                                    </div>
                                    <div class="text-muted small" data-summary-detail="presbem">
                                        @if($presbemSelectedCandidate)
                                            Suara kamu sudah tercatat.
                                        @elseif(!$presbemEligible)
                                            Kamu sudah memiliki SKL sehingga tidak dapat mengikuti pemilihan ini.
                                        @else
                                            Silakan memilih terlebih dahulu.
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-link btn-sm px-0 stepper-edit"
                                    data-target-step="1" data-summary-edit="presbem"
                                    @if($presbemVote || !$stepperConfig['presbem']['is_open'] || !$presbemEligible) disabled @endif>
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
                                        @elseif(!$calegEligible && $hasSklRestriction)
                                            Tidak dapat memilih
                                        @elseif(!$calegEligible)
                                            Tidak diwajibkan
                                        @else
                                            Belum dipilih
                                        @endif
                                    </div>
                                    <div class="text-muted small" data-summary-detail="caleg">
                                        @if($calegSelectedCandidate)
                                            Suara kamu sudah tercatat.
                                        @elseif(!$calegEligible && $hasSklRestriction)
                                            Kamu sudah memiliki SKL sehingga tidak dapat mengikuti pemilihan legislatif.
                                        @elseif(!$calegEligible)
                                            Prodi kamu tidak tercantum dalam dapil aktif.
                                        @else
                                            Silakan pilih salah satu calon legislatif sebelum melanjutkan.
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
                        <button type="button" class="btn btn-primary px-4 stepper-next" data-action="next">
                            Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('js')
    @if(!$hasFinishedPemilihan)
        <script src="{{ asset('custom/js/dashboard/pemilihan.js') }}?q{{ Str::random(5) }}"></script>
    @endif
@endpush
