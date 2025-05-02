@extends('landingpage.template')

@push('css')
<style>
    .faq {
        background: url("{{ asset('landingpage/assets/img/bg-white-3.png') }}") 0 0 repeat;
    }
    .service-item{
        border: 2px solid var(--color-secondary);
        border-radius: 5px;
        width: 100%;
        padding: 10px;
    }
    .fa{
        color: var(--color-secondary);
    }
</style>
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">

            <h2>Kontak</h2>
            <p>
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('contact.landingPage')}}" class="text-white">contact</a>
            </p>
        </div>
    </section>

    <main id="main">
        <section id="faq" class="faq">
            <div class="container">
                <div class="row gy-4">
                    @foreach ($contact as $c)
                        <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out">
                            <div class="service-item position-relative">
                                <h4>
                                    <i class="fa fa-phone"></i>
                                    <a href="{{$c->link}}" target="_blank" class="stretched-link">{{$c->name}}</a>
                                </h4>
                            </div>
                        </div><!-- End Service Item -->
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection