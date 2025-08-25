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
    #faq {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 80px 0;
        position: relative;
    }

    #faq::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
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
    /* Custom spacing for contact section */
#faq .row {
    margin: 0 -5px; /* Reduce horizontal spacing */
}

#faq .col-xl-3, #faq .col-md-6 {
    padding: 0 5px 10px 5px; /* Minimal horizontal padding, small bottom margin */
}

/* Contact Items Styling - Updated for larger boxes and smaller gaps */
.service-item {
    background: white;
    border-radius: 15px;
    padding: 40px 30px; /* Increased padding from 25px to 40px 30px */
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    cursor: pointer;
    height: 100%;
    position: relative;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    min-height: 70px; /* Added minimum height for consistency */
}

.service-item::before {
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

.service-item:hover::before {
    transform: scaleX(1);
}

.service-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.service-item h4 {
    margin-bottom: 0;
    font-size: 1.3rem; /* Increased from 1.1rem to 1.3rem */
    font-weight: 600; /* Added font weight for better visibility */
}

.service-item h4 a {
    color: #1e293b;
    text-decoration: none;
    transition: color 0.3s ease;
}

.service-item:hover h4 a {
    color: var(--color-secondary);
}

.service-item i {
    font-size: 3rem; /* Increased from 2rem to 3rem */
    color: var(--color-secondary);
    margin-bottom: 20px; /* Increased from 15px to 20px */
    display: block;
} display: block;
    }
    /* Animations */
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

            <h2 class="text-white fw-bold mb-3" data-aos="fade-up">Kontak</h2>
            <p class="text-white" data-aos="fade-up" data-aos-delay="100">
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('contact.landingPage')}}" class="text-white">Kontak</a>
            </p>
        </div>
    </section>

    <main id="main">
        <section id="faq" class="faq">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <h2>Hubungi Kami</h2>
                </div>
                <div class="row g-0">
                    @foreach ($contact as $index => $c)
                        <div class="col-xl-3 col-md-6 d-flex p-1" data-aos="zoom-out" data-aos-delay="{{ $index * 100 }}">
                            <div class="service-item position-relative">
                                <i class="fa fa-phone mb-3"></i>
                                <h4>
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
