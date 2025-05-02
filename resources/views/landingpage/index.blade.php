@extends('landingpage.template')

@push('css')
<style>
    .service-item {
        border: 2px solid var(--color-secondary);
        border-radius: 5px;
        width: 100%;
    }

    .layanan {
        background: url("landingpage/assets/img/bg-white-3.png") 0 0 repeat;
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
    <div class="layanan py-5">
        <!-- File: landingpage/berita/index.blade.php -->
<section id="berita" class="featured-services">
    <div class="container">
        <div class="section-header">
            <h2>Berita</h2>
        </div>
        <div class="row">
            @foreach($berita as $b)
            <div class="col-md-4 mb-4">
                <div class="card h-100" data-aos="fade-up" data-aos-duration="500"
                    style="box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: all 0.3s ease-in-out; transform: scale(1); {{ $b->PDF ? 'cursor: pointer;' : '' }}"
                    onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)'; this.style.transform='scale(1.03)'"
                    onmouseout="this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'; this.style.transform='scale(1)'"
                    @if($b->PDF) onclick="window.open('{{ asset('storage/'.$b->PDF) }}', '_blank')" @endif>
                    <div class="image-wrapper" style="height: 200px; overflow: hidden;">
                        <img src="{{ asset('storage/'.$b->gambar) }}" class="card-img-top" alt="{{ $b->judul }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $b->judul }}</h5>
                        <p class="card-text flex-grow-1">{{ Str::limit($b->deskripsi, 100) }}</p>
                        <p class="card-text">
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}
                            </small>
                        </p>
                        @if($b->PDF)
                        <p class="card-text mt-2">
                            <small class="text-primary">
                                <i class="fa fa-file-pdf"></i> Klik untuk membuka PDF
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

    <!-- Layanan -->
    <div class="layanan">
        @foreach (getLayanan() as $kategori)
        <section id="{{$kategori->name}}-services" class="featured-services">
            <div class="container">
                <div class="section-header">
                    <h2>Layanan {{$kategori->name}}</h2>
                </div>
                <div class="row gy-4">
                    @foreach ($kategori->layanan as $layanan)
                    <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out">
                        <div class="service-item position-relative">
                            <h4>
                                <a href="{{$layanan->url_mhs}}" class="stretched-link">{{$layanan->name}}</a>
                            </h4>
                        </div>
                    </div><!-- End Service Item -->
                    @endforeach
                </div>
            </div>
        </section>
        @endforeach
    </div>
</main><!-- End #main -->
@endsection
