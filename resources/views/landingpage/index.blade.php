@extends('landingpage.template')

@push('css')
<style>
    .service-item{
        border: none;
        border-radius: 15px;
        width: 100%;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        position: relative;
    }

    .service-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
    }

    .layanan {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        padding: 60px 0;
    }

    .service-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .service-card:hover::before {
        transform: scaleX(1);
    }

    .service-card h4 a {
        text-decoration: none;
        color: #1e293b;
        font-weight: 700;
        font-size: 1.1rem;
        line-height: 1.4;
        transition: color 0.3s ease;
    }

    .service-card:hover h4 a {
        color: var(--color-secondary);
    }

    .service-card .service-description {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 8px;
        opacity: 0.8;
    }

    .icon-wrapper {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--color-secondary) 0%, #3b82f6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .service-card:hover .icon-wrapper {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
    }

    .icon-wrapper i {
        font-size: 1.8rem;
        color: white;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-header h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }

    .section-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
        border-radius: 2px;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    @media (max-width: 768px) {
        .service-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .section-header h2 {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">
            <h2>{{ env('APP_NAME') }}</h2>
            <p>Universitas Sebelas Maret</p>
        </div>
    </section>

    <main id="main">
        <!-- Card Berita -->
        <div class="layanan">
        <!-- File: landingpage/berita/index.blade.php -->
            <section id="berita" class="featured-services">
                <div class="container">
                    <div class="section-header">
                        <h2>INFORMASI PENTING</h2>
                    </div>
                    <div class="row">
                        @foreach($berita as $b)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100" data-aos="fade-up" data-aos-duration="500"
                                style="box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: all 0.3s ease-in-out; transform: scale(1); cursor: pointer;"
                                onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)'; this.style.transform='scale(1.03)'"
                                onmouseout="this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'; this.style.transform='scale(1)'"
                                onclick="window.location.href='{{ route('berita.detail', $b->id) }}'">
                                <div class="image-wrapper" style="height: 200px; overflow: hidden;">
                                    <img src="{{ $b->gambar ? asset('storage/'.$b->gambar) : asset('/back/assets/images/Default_News.png') }}"
                                         class="card-img-top" alt="{{ $b->judul }}"
                                         style="width: 100%; object-fit: cover;">
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><strong>{{ $b->judul }}</strong></h5>
                                    <p class="card-text flex-grow-1">{{ implode(' ', array_slice(explode(' ', $b->deskripsi), 0, 15))}}{{ strlen($b->deskripsi) > strlen(implode(' ', array_slice(explode(' ', $b->deskripsi), 0, 15))) ? '...' : '' }}</p>

                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}
                                        </small>
                                    </p>
                                    @if($b->PDF)
                                    <p class="card-text mt-2">
                                        <small class="text-primary">
                                            <i class="fa fa-file-pdf"></i> Dokumen tersedia
                                        </small>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div><!-- End Card Berita -->
                        @endforeach
                    </div>

                    <!-- Tambahkan Pagination -->
                    <div class="d-flex justify-content-end mt-4">
                        {{ $berita->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </section>
        </div><!-- End Container -->

        <div class="layanan">
            @foreach (getLayanan() as $kategori)
                <section id="{{$kategori->name}}-services" class="featured-services">
                    <div class="container">
                        <div class="section-header">
                            <h2>Layanan {{$kategori->name}}</h2>
                        </div>
                        <div class="service-grid">
                            @foreach ($kategori->layanan as $layanan)
                            @php
                                $user = auth()->user();
                                $isStaff = $user && $user->roles && $user->roles->gate_name == 'staff';
                                
                                // Icon mapping untuk setiap layanan
                                $icons = [
                                    'Verifikasi Wisuda' => 'fas fa-graduation-cap',
                                    'Surat Keterangan' => 'fas fa-file-alt',
                                    'Transkrip Nilai' => 'fas fa-scroll',
                                    'Legalisir' => 'fas fa-stamp',
                                    'default' => 'fas fa-cogs'
                                ];
                                $icon = $icons[$layanan->name] ?? $icons['default'];
                            @endphp
                            <div data-aos="fade-up" data-aos-delay="100">
                                <div class="service-item position-relative service-card">
                                    <div class="icon-wrapper">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                    
                                    <h4 class="mb-2">
                                        @if($layanan->name == 'Verifikasi Wisuda')
                                            <a href="/verifikasiWisuda/informasi" class="stretched-link">{{$layanan->name}}</a>
                                        @else
                                            <a href="{{$layanan->url_mhs}}" class="stretched-link">{{$layanan->name}}</a>
                                        @endif
                                    </h4>
                                    <p class="service-description">Klik untuk mengakses layanan</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </main><!-- End #main -->
@endsection
