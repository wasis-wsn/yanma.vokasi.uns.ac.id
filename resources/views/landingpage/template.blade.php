<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title')</title>
    <meta content="Layanan Akademik SV UNS adalah website portal pelayanan Sekolah Vokasi UNS untuk mahasiswa Sekolah Vokasi UNS" name="description">
    <meta content="layanan, sv, sekolah vokasi, vokasi, uns, sekolah vokasi uns, universitas sebelas maret" name="keywords">

    <!-- Favicons -->
    {{-- <link href="{{ asset('logo-sv.png') }}" rel="favicon"> --}}
    <link rel="shortcut icon" href="{{asset('logo-sv.png')}}" type="image/x-icon">
    {{-- <link href="{{ asset('SIKASI2.png') }}" rel="apple-touch-icon"> --}}

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    {{-- {{ asset('assets/img/logo.png') }} --}}
    <link href="{{ asset('landingpage/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet') }}">

    <!-- Variables CSS Files. Uncomment your preferred color scheme -->
    <link href="{{ asset('landingpage/assets/css/variables-blue.css') }}" rel="stylesheet">


    <!-- Template Main CSS File -->
    <link href="{{ asset('landingpage/assets/css/main.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


    @stack('css')
    <!-- =======================================================
  * Template Name: landingpage/assets - v2.1.0
  * Template URL: https://bootstrapmade.com/landingpage/assets-bootstrap-business-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    {{-- <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div> --}}

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top" data-scrollto-offset="0">
        <div class="container-fluid d-flex align-items-center justify-content-between">

            <a class="navbar-brand">
                <img src="{{asset('sekolahvokasi.png')}}" width="" height="39">
            </a>
            <nav id="navbar" class="navbar">
                <ul>
                    @if (Request::is('/'))
                        <li><a class="nav-link scrollto" href="#Akademik-services">Akademik</a></li>
                        <li><a class="nav-link scrollto" href="#Kemahasiswaan-services">Kemahasiswaan</a></li>
                        <li><a class="nav-link scrollto" href="#Alumni-services">Alumni</a></li>
                    @else
                        <li><a class="nav-link scrollto {{ Request::is('/') ? 'active' : '' }}" href="{{route('home')}}">Beranda</a></li>
                    @endif
                    <li><a class="nav-link scrollto {{ Request::is('akreditasi/lp') ? 'active' : '' }}" href="{{route('akreditasi.landingPage')}}">Akreditasi</a></li>
                    <!--<li><a class="nav-link scrollto" href="https://peminjaman.vokasi.uns.ac.id">Peminjaman Tempat</a></li>-->
                    <li><a class="nav-link scrollto {{ Request::is('contact/lp') ? 'active' : '' }}" href="{{route('contact.landingPage')}}">Kontak</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle d-none"></i>
            </nav><!-- .navbar -->
            @if (Auth::check())
            <a class="btn-getstarted scrollto" href="{{route('dashboard')}}">Dashboard</a>
            @else
            <a class="btn-getstarted scrollto" href="{{route('login')}}">Login</a>
            @endif

        </div>
    </header><!-- End Header -->


    @yield('content')

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="footer-content">
            <div class="container">
                <div class="row">
        
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-info">
                            <h3>Sekolah Vokasi UNS</h3>
                            <p>
                                Kampus Tirtomoyo, Universitas Sebelas Maret<br>
                                Jl. Kolonel Sutarto 150 K, Jebres, Surakarta<br><br>
                            <strong>Phone:</strong> 0271-664126<br>
                            <strong>Email:</strong> vokasi@unit.uns.ac.id<br>
                            </p>
                        </div>
                    </div>
        
                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4>Website UNS</h4>
                        <ul>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://uns.ac.id/">UNS</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://siakad.uns.ac.id/">SIAKAD</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://sso.uns.ac.id/module.php/uns/index.php">SSO</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://ocw.uns.ac.id/">OCW</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://profil.uns.ac.id/">Profil</a></li>
                        </ul>
                    </div>
        
                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Website Sekolah Vokasi UNS</h4>
                        <ul>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://portal.vokasi.uns.ac.id/">Portal</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://vokasi.uns.ac.id/">Sekolah Vokasi</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://cdc.vokasi.uns.ac.id/">CDC</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://simonsi.vokasi.uns.ac.id/">SIMONSI</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://sikasi.vokasi.uns.ac.id/">SIKASI</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="https://peminjaman.vokasi.uns.ac.id/">Peminjaman</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-legal text-center">
            <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
                <div class="d-flex flex-column align-items-center align-items-lg-start">
                    <div class="copyright">
                        &copy; Copyright <strong><span>Sekolah Vokasi | UNS</span></strong>.
                    </div>
                </div>
                <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
                    <a href="https://twitter.com/vokasi_uns" class="twitter"><i class="bi bi-twitter"></i></a>
                    <a href="https://www.facebook.com/vokasi.uns" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/vokasiuns" class="instagram"><i
                            class="bi bi-instagram"></i></a>
                    <a href="https://www.youtube.com/channel/UCz7StIjWe4osVgpG_ErbvWQ" class="youtube"><i
                            class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    {{-- <div id="preloader"></div> --}}

    <!-- Vendor JS Files -->
    <script src="{{ asset('landingpage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="https://kit.fontawesome.com/a62c621401.js" crossorigin="anonymous"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('landingpage/assets/js/main.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        @if (session('error'))
            toastr.error("{{session('error')}}")
        @endif
    </script>

    @stack('js')
</body>

</html>
