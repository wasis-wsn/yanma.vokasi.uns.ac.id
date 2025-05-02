<!doctype html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Login </title>
        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('logo-sv.png')}}" />
        
        <!-- Library / Plugin Css Build -->
        <link rel="stylesheet" href="{{asset('back/assets/css/core/libs.min.css')}}" />
        
        <!-- Hope Ui Design System Css -->
        <link rel="stylesheet" href="{{asset('back/assets/css/hope-ui.min.css?v=1.1.0')}}" />
        
        <!-- Custom Css -->
        <link rel="stylesheet" href="{{asset('back/assets/css/custom.min.css?v=1.1.0')}}" />
        
        <!-- Dark Css -->
        <link rel="stylesheet" href="{{asset('back/assets/css/dark.min.css')}}"/>
        
        <!-- RTL Css -->
        <link rel="stylesheet" href="{{asset('back/assets/css/rtl.min.css')}}"/>

        <style>
            .logo-uns{
                overflow-clip-margin: content-box;
                overflow: clip;
                vertical-align: middle;
                margin-right: .5rem!important;
                position: relative;
                display: inline-block;
                width: 2.625rem;
                height: 2.625rem;
                border-radius: .5rem;
                width: 1rem;
                height: 1rem;
            }
            .form-group .form-control, 
            .form-group .form-select{
                border: 1px solid #8A92A6;
                color: #8A92A6;
            }
        </style>
    </head>

    <body class=" " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
        <!-- loader Start -->
        <div id="loading">
            <div class="loader simple-loader">
                <div class="loader-body"></div>
            </div>
        </div>
        <!-- loader END -->
        <div class="wrapper">
            <section class="login-content">
                <div class="row m-0 align-items-center bg-white vh-100">
                    <div class="col-md-6">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                    <div class="card-body">
                                        <a href="#" class="navbar-brand d-flex align-items-center mb-3">
                                            <!--Logo start-->
                                            <img src="{{asset('logosv.png')}}" height="40">
                                            <!--logo End-->
                                            <!--<h5 class="logo-title ms-3">SIAP SV UNS</h5>-->
                                        </a>
                                        <h2 class="mb-2 text-center">Login</h2>
                                        <p class="text-center">Login untuk menggunakan layanan</p>
                                        @error('email')
                                            <div class="alert alert-danger" role="alert" style="margin-bottom: 0; padding: 0; text-align: center; border:none;">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                        <form class="user" method="POST" action="{{ route('prosesLogin') }}">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="email" id="email" aria-describedby="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email" value="{{ old('email') }}" required
                                                            autocomplete="email" autofocus>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="password" class="form-label">Password</label>
                                                        <input type="password" id="password" aria-describedby="password"
                                                            class="form-control  @error('email') is-invalid @enderror"
                                                            name="password" required autocomplete="current-password">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-grid mb-4">
                                                <button type="submit" class="btn btn-primary" name="login-button">
                                                    <i class="fa fa-arrow-right-to-bracket"></i> Log in
                                                </button>
                                            </div>
                                            <p class="text-center my-3 px-0">Mahasiswa silakan log in menggunakan email student.uns.ac.id</p>
                                            <div class="d-grid">
                                                <a class="btn btn-outline-primary" href="{{route('google.login')}}">
                                                <span class="d-flex justify-content-center align-items-center" style="text-transform: none">
                                                    <img src="{{asset('logo-uns-warna.png')}}" alt="" class="logo-uns">
                                                    Log In Email student.uns.ac.id
                                                </span>
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sign-bg">
                            <img src="{{asset('logosv.png')}}" width="280" height="230" style="opacity: 0.05">
                        </div>
                    </div>
                    <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                        <img src="{{asset('back/assets/images/auth/01.png')}}" class="img-fluid gradient-main animated-scaleX"
                            alt="images">
                    </div>
                </div>
            </section>
        </div>

        <!-- Library Bundle Script -->
        <script src="{{asset('back/assets/js/core/libs.min.js')}}"></script>

        {{-- FontAwesome --}}
        <script src="https://kit.fontawesome.com/a62c621401.js" crossorigin="anonymous"></script>

        <!-- External Library Bundle Script -->
        <script src="{{asset('back/assets/js/core/external.min.js')}}"></script>
        
        <!-- Widgetchart Script -->
        <script src="{{asset('back/assets/js/charts/widgetcharts.js')}}"></script>
        
        <!-- mapchart Script -->
        <script src="{{asset('back/assets/js/charts/vectore-chart.js')}}"></script>
        <script src="{{asset('back/assets/js/charts/dashboard.js')}}" defer></script>
        
        <!-- fslightbox Script -->
        <script src="{{asset('back/assets/js/plugins/fslightbox.js')}}"></script>
        
        <!-- Settings Script -->
        <script src="{{asset('back/assets/js/plugins/setting.js')}}"></script>
        
        <!-- Form Wizard Script -->
        <script src="{{asset('back/assets/js/plugins/form-wizard.js')}}"></script>
        
        <!-- App Script -->
        <script src="{{asset('back/assets/js/hope-ui.js')}}" defer></script>
    </body>

</html>
