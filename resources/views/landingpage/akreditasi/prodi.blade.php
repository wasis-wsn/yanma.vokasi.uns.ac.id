@extends('landingpage.template')

@push('css')
<style>
    .prodi-hero {
        background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
        padding: 120px 0 90px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .prodi-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1400 900"><g fill="rgba(255,255,255,0.08)"><circle cx="200" cy="200" r="180"/><circle cx="1200" cy="180" r="140"/><circle cx="700" cy="700" r="220"/></g></svg>');
        pointer-events: none;
    }

    .prodi-hero-content {
        position: relative;
        z-index: 1;
    }

    .prodi-hero h1 {
        font-size: 2.8rem;
        font-weight: 700;
    }

    .prodi-hero p {
        font-size: 1.1rem;
        opacity: 0.85;
    }

    .akreditasi-list-section {
        padding: 80px 0;
        background: #f8fafc;
    }

    .akreditasi-item {
        background: #fff;
        border-radius: 18px;
        padding: 28px 32px;
        box-shadow: 0 18px 40px rgba(99, 102, 241, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.15);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .akreditasi-item::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(56, 189, 248, 0.12));
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .akreditasi-item:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 55px rgba(99, 102, 241, 0.16);
    }

    .akreditasi-item:hover::before {
        opacity: 1;
    }

    .akreditasi-item-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .periode-tag {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        border-radius: 999px;
        background: rgba(59, 130, 246, 0.12);
        color: #1d4ed8;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .meta-info {
        display: flex;
        gap: 20px;
        color: #475569;
        font-size: 0.95rem;
        flex-wrap: wrap;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        border-radius: 999px;
        background: linear-gradient(135deg, #6366f1 0%, #2563eb 100%);
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.22);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .download-btn:hover {
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(99, 102, 241, 0.28);
    }

    .other-prodi-section {
        padding: 70px 0 90px;
        background: #fff;
    }

    .chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .chip-list a {
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        background: rgba(148, 163, 184, 0.18);
        color: #334155;
        border-radius: 999px;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease;
        font-weight: 600;
    }

    .chip-list a:hover {
        background: rgba(59, 130, 246, 0.18);
        color: #1d4ed8;
    }
</style>
@endpush

@section('content')

<section class="prodi-hero">
    <div class="container prodi-hero-content" data-aos="fade-up">
        <p class="mb-3">
            <a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('akreditasi.landingPage') }}" class="text-white-50 text-decoration-none">Akreditasi</a>
            <span class="mx-2">/</span>
            <span class="fw-semibold">{{ $prodi->name }}</span>
        </p>
        <h1 class="mb-3">{{ $prodi->name }}</h1>
        <p class="mb-0">Kumpulan dokumen akreditasi resmi untuk Program Studi {{ $prodi->name }}.</p>
    </div>
</section>

<section class="akreditasi-list-section">
    <div class="container">
        @if($akreditasi->isEmpty())
            <div class="text-center py-5" data-aos="fade-up">
                <h3 class="fw-semibold text-slate-600 mb-2">Belum ada dokumen akreditasi.</h3>
                <p class="text-slate-500">Silakan periksa kembali di lain waktu.</p>
            </div>
        @else
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3" data-aos="fade-up">
                <h2 class="h4 fw-semibold mb-0">Daftar Dokumen Akreditasi</h2>
                <span class="text-slate-500">{{ $akreditasi->count() }} dokumen tersedia</span>
            </div>

            <div class="d-flex flex-column gap-4">
                @foreach($akreditasi as $index => $item)
                    <div class="akreditasi-item" data-aos="fade-up" data-aos-delay="{{ ($index % 6) * 60 }}">
                        <div class="akreditasi-item-content">
                            <div>
                                <span class="periode-tag">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    {{ $item['periode_label'] }}
                                </span>
                                <div class="meta-info mt-3">
                                    <div>
                                        <i class="fa-regular fa-clock me-1"></i>
                                        Tersedia sejak {{ optional($item['created_at'])->translatedFormat('d F Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            @if($item['file_url'])
                                <a href="{{ $item['file_url'] }}" target="_blank" class="download-btn">
                                    <i class="fa-solid fa-download"></i>
                                    Unduh Dokumen
                                </a>
                            @else
                                <span class="text-slate-400 fst-italic">File tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if($otherProdi->isNotEmpty())
    <section class="other-prodi-section">
        <div class="container" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h3 class="h5 fw-semibold mb-0">Program Studi lainnya</h3>
                <a href="{{ route('akreditasi.landingPage') }}" class="text-primary text-decoration-none">
                    Lihat semua prodi
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="chip-list">
                @foreach($otherProdi as $item)
                    <a href="{{ route('akreditasi.prodi', encodeId($item->id)) }}">
                        {{ $item->name }} ({{ $item->akreditasi_count }})
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
