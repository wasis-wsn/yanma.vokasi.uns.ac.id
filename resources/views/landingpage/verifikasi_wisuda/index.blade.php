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

    /* Content card styling */
    .content-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 30px;
        margin-bottom: 30px;
    }

    .content-card h3 {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 15px;
    }

    .content-card h3:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
        border-radius: 2px;
    }

    .content-card h5 {
        color: #334155;
        font-weight: 600;
        margin-top: 25px;
        margin-bottom: 15px;
    }

    .content-card ol {
        padding-left: 20px;
    }

    .content-card ol li {
        margin-bottom: 15px;
        color: #475569;
        line-height: 1.7;
    }

    .content-card a {
        color: var(--color-secondary);
        font-weight: 500;
        text-decoration: none;
    }

    .content-card a:hover {
        text-decoration: underline;
    }

    /* Strong text highlighting */
    .content-card b, 
    .content-card strong {
        color: #1e293b;
        background: linear-gradient(to bottom, transparent 50%, rgba(var(--color-secondary-rgb), 0.1) 50%);
        padding: 0 3px;
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

            <h2 class="text-white fw-bold mb-3" data-aos="fade-up">Verifikasi Wisuda</h2>
            <p class="text-white" data-aos="fade-up" data-aos-delay="100">
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="#" class="text-white">Verifikasi Wisuda</a>
            </p>
        </div>
    </section>

    <main id="main">
        <section>
            <div class="container">
                <div class="content-card fade-in-up" data-aos="fade-up">
                    <h3>Verifikasi Wisuda</h3>
                    <h5>Alur Baru Verifikasi Wisuda Sekolah Vokasi UNS</h5>
                    <ol>
                        <li><b>Hanya mahasiswa yang Surat Keterangan Lulus (SKL)-nya sudah terbit dan nomor ijazahnya sudah keluar</b> yang bisa mengajukan berkas verifikasi wisuda.</li>
                        <li><b>Upload berkas verifikasi wisuda dan verifikasi wisuda hanya bisa dilakukan pada saat jadwal verifikasi wisuda sudah dibuka.</b></li>
                        <li>Data akan muncul saat Akademik Sekolah Vokasi melakukan import data mahasiswa.<b> Data tersebut berdasarkan update nomor ijazah dari Akademik Pusat, bukan ditentukan oleh Akademik Sekolah Vokasi.</b></li>
                        <li>Log in ke web <a href="https://yanma.vokasi.uns.ac.id/login" target="_blank">https://yanma.vokasi.uns.ac.id/</a> menggunakan email SSO.</li>
                        <li>Pilih menu "Verifikasi Wisuda".</li>
                        <li>Upload berkas persyaratan verifikasi wisuda.</li>
                        <li><b>Mohon ditunggu dan cek secara berkala</b> di web yanma untuk <b>validasi dokumen</b> oleh Akademik Sekolah Vokasi.</li>
                        <li>Jika berkas sudah divalidasi oleh Akademik Sekolah Vokasi, mahasiswa bisa <b>segera melakukan konfirmasi kehadiran wisuda.</b></li>
                        <li>Mahasiswa yang memilih konfirmasi kehadiran <b>"Bersedia"</b> ajuan akan diverifikasi oleh Akademik Sekolah Vokasi untuk mengikuti wisuda pada periode tersebut.</li>
                        <li>Mahasiswa yang memilih konfirmasi kehadiran <b>"Tidak Bersedia"</b> ajuan tidak akan diverifikasi untuk wisuda periode tersebut dan <b>akan masuk dalam antrian verifikasi wisuda pada periode berikutnya.</b></li>
                        <li>Status verifikasi dapat dilihat diweb yanma atau bisa juga cek berkala di web <a href="https://wisuda.uns.ac.id/" target="_blank">https://wisuda.uns.ac.id/</a> (log in web wisuda > pilih menu sinkron data > scroll kebawah, cari kolom "Pin Kode Akses Wisuda"), <b>jika sudah ada angkanya berarti sudah diverifikasi oleh Akademik Sekolah Vokasi.</b></li>
                        <li>Jika sudah diverifikasi, <b>cek data pada menu sinkron data dan silahkan segera melakukan sinkronisasi data.</b></li>
                        <li><b>Cetak draft ijazah</b> diweb wisuda pada menu Cetak > Draft Ijazah.</li>
                        <li>Bagi mahasiswa <b>yang sudah diverifikasi tidak bisa mengundurkan diri</b> dengan alasan apapun.</li>
                        <li><b>Wajib bergabung digrup telegram wisuda</b> (link grup ada dihalaman awal web wisuda).</li>
                    </ol>
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