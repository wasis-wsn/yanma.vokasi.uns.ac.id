@extends('landingpage.template')

@push('css')
<style>
    .service-item{
        border: 2px solid var(--color-secondary);
        border-radius: 5px;
        width: 100%
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

        <div class="layanan">
            @foreach (getLayanan() as $kategori)
                <section id="{{$kategori->name}}-services" class="featured-services">
                    <div class="container">
                        <div class="section-header">
                            <h2>Layanan {{$kategori->name}}</h2>
                        </div>
                        <div class="row gy-4">
                            @foreach ($kategori->layanan as $layanan)
                            @php
                                $user = auth()->user();
                                $isStaff = $user && $user->roles && $user->roles->gate_name == 'staff';
                            @endphp
                            @if ($layanan->name != 'Verifikasi Wisuda' || $isStaff)
                                <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out">
                                    <div class="service-item position-relative">
                                        <h4>
                                            <a href="{{$layanan->url_mhs}}" class="stretched-link">{{$layanan->name}}</a>
                                        </h4>
                                    </div>
                                </div><!-- End Service Item -->
                            @endif
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </main><!-- End #main -->
@endsection