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

    .accordion-item {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 16px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .accordion-button {
        background: white;
        border: none;
        font-weight: 600;
        padding: 18px 24px;
        color: #1e293b;
        transition: all 0.3s ease;
    }

    .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: var(--color-secondary);
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(var(--color-secondary-rgb), 0.5);
    }

    .accordion-body {
        padding: 20px 24px;
        background: #ffffff;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .accordion-body a {
        background: linear-gradient(135deg, var(--color-secondary) 0%, #3b82f6 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .accordion-body a:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }

    /* Search input styling */
    .input-group {
        margin-bottom: 30px;
        border-radius: 50px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .input-group-text {
        border-radius: 50px 0 0 50px;
        background: white;
        border: none;
        padding-left: 20px;
    }

    .form-control {
        border-radius: 0 50px 50px 0;
        padding: 12px 20px;
        border: none;
        font-size: 1rem;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: var(--color-secondary);
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

            <h2 class="text-white fw-bold mb-3" data-aos="fade-up">Akreditasi</h2>
            <p class="text-white" data-aos="fade-up" data-aos-delay="100">
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('akreditasi.landingPage')}}" class="text-white">Akreditasi</a>
            </p>
        </div>
    </section>

    <main id="main">
        <section id="faq" class="faq">
            <div class="container-fluid" data-aos="fade-up">
                <div class="section-header">
                    <h2 class="text-dark" data-aos="fade-up">Gunakan Dokumen Akreditasi sesuai dengan tahun kelulusan</h2>
                </div>

                <div class="container" data-aos="fade-up" data-aos-delay="200">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-end-0" style="height: 100%"><i class="bi bi-search"></i></span>
                        </div>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari Prodi">
                        <div class="dropdown search-dropdown list-group" id="searchDropdown">
                        </div>
                    </div>
                </div>

                <div class="row gy-4">
                    <div class="col d-flex flex-column justify-content-center align-items-stretch order-2 order-lg-1">
                        <div class="accordion accordion-flush px-xl-5" id="faqlist">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
@push('js')
    <script>
        function accordionBody(akreditasi) {
            let akreditasiLinks = '';
            akreditasi.forEach(item => {
                akreditasiLinks += `
                    <a href="${'{{ asset('storage/akreditasi/') }}/' + item.file}" target="_blank" class="btn btn-info my-2">
                        ${item.tahun}
                    </a>
                `;
            });

            return `
                <div class="accordion-body">
                    ${akreditasiLinks}
                </div>
            `;
        }

        function accordionItem(prodi) {
            return `
                <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-${prodi.id}">
                            ${prodi.name}
                        </button>
                    </h3>
                    <div id="faq-content-${prodi.id}" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                        ${accordionBody(prodi.akreditasi)}
                    </div>
                </div>
            `
        }

        function getData(q) {
            let url = q ? `{{ route("akreditasi.landingPage.getData") }}?prodi=${q}` :  `{{ route("akreditasi.landingPage.getData") }}`;
            $.get(url)
            .done(function(prodis) {
                let accordionProdi = '';
                prodis.forEach(prodi => {
                    accordionProdi += accordionItem(prodi);
                });
                $('#faqlist').empty();
                $('#faqlist').html(accordionProdi);
            })
            .fail(function() {
                console.error("Terjadi kesalahan saat mengambil data.");
            });

        }
    </script>
    <script>
        $(document).ready(function () {
            const searchInput = $('#searchInput');
            const prodiList = $('#faqlist');

            getData();

            // Definisikan fungsi debounce
            function debounce(func, wait, immediate) {
                let timeout;
                return function() {
                    let context = this, args = arguments;
                    let later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    let callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            }

            // Terapkan debounce pada fungsi input
            const delayedSearch = debounce(function() {
                const keyword = searchInput.val().toLowerCase();
                prodiList.empty();

                if (keyword.trim() !== '') {
                    getData(keyword);
                } else {
                    getData();
                }
            }, 500);

            // Terapkan debounce pada input event
            searchInput.on('input', delayedSearch);
        })
    </script>
@endpush
