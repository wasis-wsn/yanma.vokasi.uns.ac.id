@extends('landingpage.template')

@push('css')
<style>
    /* Hero Section Styles */
    #hero-animated {
        background: linear-gradient(135deg, #3b82f6 0%, #b6cff6 100%);
        min-height: 50vh;
        position: relative;
        overflow: hidden;
    }

    #hero-animated::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff08" points="0,1000 1000,0 1000,1000"/></svg>');
        pointer-events: none;
    }

    /* Floating particles animation */
    .particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .particle:nth-child(1) {
        width: 80px;
        height: 80px;
        left: 10%;
        animation-delay: 0s;
        animation-duration: 8s;
    }

    .particle:nth-child(2) {
        width: 120px;
        height: 120px;
        left: 80%;
        animation-delay: 2s;
        animation-duration: 10s;
    }

    .particle:nth-child(3) {
        width: 60px;
        height: 60px;
        left: 60%;
        animation-delay: 4s;
        animation-duration: 7s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        50% {
            transform: translateY(-100px) rotate(180deg);
            opacity: 0.8;
        }
    }

    /* Animated geometric shapes */
    .hero-shapes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .shape {
        position: absolute;
        opacity: 0.1;
    }

    .shape-1 {
        top: 15%;
        left: 15%;
        width: 150px;
        height: 150px;
        background: white;
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        animation: morph 8s ease-in-out infinite;
    }
    
    .shape-3 {
        top: 40%;
        right: 20%;
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes morph {
        0%, 100% {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            transform: rotate(0deg);
        }
        50% {
            border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
            transform: rotate(180deg);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.3;
        }
    }
    
    /* Content styling */
    #main {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 60px 0;
        position: relative;
    }

    #main::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    /* UKT Card Styling */
    .ukt-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 40px;
        margin-bottom: 30px;
        position: relative;
    }

    .ukt-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
    }

    .jenis-ukt {
        font-size: 2.5rem;
        font-weight: 800;
        text-align: center;
        color: #1e293b;
        margin-bottom: 20px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.2;
    }

    .teks-arial {
        font-family: Arial, sans-serif;
        text-align: center;
        color: #475569;
        margin: 25px 0 10px;
    }

    .tgl-ukt {
        font-weight: 700;
        text-decoration: underline;
        color: var(--color-secondary);
        text-align: center;
        font-size: 1.25rem;
        margin-bottom: 20px;
    }

    .ukt-card p {
        color: #475569;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .ukt-card h2 {
        color: #1e293b;
        font-size: 1.8rem;
        text-align: center;
        margin: 40px 0 20px;
        font-weight: 700;
    }

    /* Animation classes */
    .fade-in-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease-out;
    }

    .fade-in-up.visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <!-- Floating Particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <!-- Animated Shapes -->
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-3"></div>
        </div>

        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">

            <h2 class="text-white fw-bold mb-3" data-aos="fade-up">Keringanan UKT</h2>
            <p class="text-white" data-aos="fade-up" data-aos-delay="100">
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('ukt.landingPage')}}" class="text-white">Keringanan UKT</a>
            </p>
        </div>
    </section>

    <main id="main">
        @foreach ($ukt as $index => $item)
        <div class="container py-4">
            <div class="ukt-card fade-in-up" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <h1 class="jenis-ukt teks-arial">Keringanan UKT <br> ({{$item['jenis']}})</h1>
                <div>
                    {!! $item['keterangan'] !!}
                </div>
                <br>
                <h4 class="teks-arial">Jadwal Pengajuan Keringanan UKT oleh Mahasiswa dimulai tanggal :</h4>
                <h4 class="tgl-ukt">{{ $item['pengajuan'] }}</h4>
                <h4 class="teks-arial">Jadwal Verifikasi Keringanan UKT oleh Fakultas dimulai tanggal :</h4>
                <h4 class="tgl-ukt">{{ $item['verif_fakultas'] }}</h4>
                <h4 class="teks-arial">Jadwal Verifikasi Keringanan UKT oleh Universitas dimulai tanggal :</h4>
                <h4 class="tgl-ukt">{{ $item['verif_univ'] }}</h4>
                <br>
                <h2>Persyaratan</h2>
                <div>
                    {!! $item['persyaratan'] !!}
                </div>
            </div>
        </div>
        @endforeach
    </main>
@endsection

@push('js')
<script>
    // Observer for animations
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all elements with animation classes
        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });
    });
</script>
@endpush