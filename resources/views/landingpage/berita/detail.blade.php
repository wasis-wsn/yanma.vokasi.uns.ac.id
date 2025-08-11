@extends('landingpage.template')

@push('css')
<style>
    .news-hero {
        height: 60vh;
        min-height: 400px;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .news-hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
    }

    .news-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
        max-width: 800px;
        padding: 0 20px;
    }

    .news-hero-title {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .news-hero-meta {
        font-size: 1.1rem;
        opacity: 0.9;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    @media (max-width: 768px) {
        .news-hero-title {
            font-size: 1.8rem;
        }
        .news-hero {
            height: 50vh;
            min-height: 300px;
        }
    }
</style>
@endpush

@section('content')
<main id="main">
    <!-- Hero Section with News Image -->
    <section class="news-hero" style="background-image: url('{{ $berita->gambar ? asset('storage/'.$berita->gambar) : asset('/back/assets/images/Default_News.png') }}');">
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content" data-aos="fade-up">
            <h1 class="news-hero-title">{{ $berita->judul }}</h1>
            <div class="news-hero-meta">
                <i class="fa fa-calendar"></i>
                {{ \Carbon\Carbon::parse($berita->tanggal)->format('d F Y') }}
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <div style="background: url('{{ asset('landingpage/assets/img/bg-white-3.png') }}') 0 0 repeat; padding: 80px 0;">
        <div class="container">
            <div style="margin-bottom: 30px;">
                <a href="javascript:history.back();"
                style="display: inline-flex; align-items: center; padding: 10px 20px; background: transparent; color: #007bff; border: 1px solid #007bff; text-decoration: none; border-radius: 5px; font-weight: 500; transition: all 0.3s ease;"
                onmouseover="this.style.background='#007bff'; this.style.color='white';"
                onmouseout="this.style.background='transparent'; this.style.color='#007bff';">
                    <i class="fa fa-arrow-left" style="margin-right: 8px;"></i> Kembali
                </a>
            </div>

            <div style="background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 40px; margin-bottom: 30px;" data-aos="fade-up">
                <div style="line-height: 1.8; color: #555; font-size: 16px; margin-bottom: 30px;">
                    {!! nl2br(e($berita->deskripsi)) !!}
                </div>

                @if($berita->PDF)
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-top: 30px; text-align: center;">
                    <h5 style="margin-bottom: 15px;"><i class="fa fa-file-pdf" style="color: #dc3545;"></i> Dokumen Terlampir</h5>
                    <p style="margin-bottom: 20px; color: #6c757d;">Klik tombol di bawah untuk membuka atau mengunduh dokumen PDF</p>
                    <a href="{{ asset('storage/'.$berita->PDF) }}"
                       target="_blank"
                       style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; transition: all 0.3s ease;"
                       onmouseover="this.style.background='#c82333'; this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='#dc3545'; this.style.transform='translateY(0)';">
                        <i class="fa fa-download" style="margin-right: 8px;"></i> Buka PDF
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection
