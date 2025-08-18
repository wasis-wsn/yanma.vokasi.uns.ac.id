<!doctype html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Login </title>
        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('logo-sv.png')}}" />

        <!-- FontAwesome - Moving to head for earlier loading -->
        <script src="https://kit.fontawesome.com/a62c621401.js" crossorigin="anonymous"></script>

        <!-- Fallback for FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
            body {
                background: linear-gradient(135deg, #2155CD, #0066ff, #3a7bd5) !important;
                overflow: hidden;
                position: relative;
            }

            /* Floating shapes animation - reduced to 2 */
            .shape {
                position: absolute;
                background-color: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(5px);
                border-radius: 50%;
                z-index: -1;
            }

            .shape-1 {
                width: 350px;
                height: 350px;
                top: -5%;
                left: -5%;
                animation: float-large 20s infinite ease-in-out;
            }

            .shape-2 {
                width: 400px;
                height: 400px;
                bottom: -10%;
                right: -5%;
                animation: float-reverse 25s infinite ease-in-out;
            }

            @keyframes float-large {
                0% {
                    transform: translateY(0) rotate(0);
                }
                50% {
                    transform: translateY(-30px) rotate(8deg);
                }
                100% {
                    transform: translateY(0) rotate(0);
                }
            }

            @keyframes float-reverse {
                0% {
                    transform: translateY(0) rotate(0);
                }
                50% {
                    transform: translateY(30px) rotate(-5deg);
                }
                100% {
                    transform: translateY(0) rotate(0);
                }
            }

            /* Pattern overlay */
            .pattern-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1IiBoZWlnaHQ9IjUiPgo8cmVjdCB3aWR0aD0iNSIgaGVpZ2h0PSI1IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiPjwvcmVjdD4KPHBhdGggZD0iTTAgNUw1IDAiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLW9wYWNpdHk9IjAuMDUiPjwvcGF0aD4KPC9zdmc+');
                opacity: 0.3;
                z-index: -2;
            }

            body {
                background-color: #1a3b8b !important;
                overflow: hidden;
            }

            .login-content {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            .auth-card {
                background-color: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border-radius: 15px;
                padding: 25px;
                max-width: 450px;
                width: 100%;
                border: 1px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 15px 35px 0 rgba(0, 0, 0, 0.2);
            }

            .user-icon {
                background-color: #3b82f6;
                width: 70px;
                height: 70px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0 auto 20px;
                color: white;
                font-size: 30px;
            }

            .input-with-icon {
                position: relative;
                margin-bottom: 20px;
            }

            .input-with-icon .form-control {
                padding: 12px 15px 12px 45px;
                border-radius: 50px;
                background-color: rgba(255, 255, 255, 0.9);
                border: none;
                height: auto;
                font-size: 14px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .input-with-icon .form-control:focus {
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                outline: none;
            }

            .input-with-icon .form-control::placeholder {
                color: #8A92A6;
                font-weight: 400;
            }

            .input-with-icon i {
                position: absolute;
                left: 20px;
                top: 50%;
                transform: translateY(-50%);
                color: #3b82f6;
                font-size: 16px;
                z-index: 2;
            }

            .input-with-icon .toggle-password {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #8A92A6;
                z-index: 2;
            }

            .btn-masuk {
                background-color: #ff6d20;
                border-color: #ff6d20;
                color: white;
                border-radius: 50px;
                padding: 12px;
                font-weight: 600;
                font-size: 15px;
                box-shadow: 0 4px 15px rgba(255, 109, 32, 0.3);
                transition: all 0.3s ease;
            }

            .btn-masuk:hover {
                background-color: #e55e15;
                border-color: #e55e15;
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(255, 109, 32, 0.4);
            }

            .divider {
                display: flex;
                align-items: center;
                text-align: center;
                margin: 15px 0;
            }

            .divider::before,
            .divider::after {
                content: '';
                flex: 1;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }

            .divider span {
                padding: 0 10px;
                color: #ffffff;
            }

            .btn-student-login {
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: white;
                border-radius: 50px;
                padding: 12px;
                gap: 10px;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-student-login:hover {
                background-color: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }

            .copyright {
                text-align: center;
                color: rgba(255, 255, 255, 0.7);
                font-size: 12px;
                margin-top: 15px;
            }

            h2, p {
                color: white;
            }
        </style>
    </head>

    <body>
        <!-- Floating shapes - reduced to 2 larger shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>

        <!-- Pattern overlay -->
        <div class="pattern-overlay"></div>

        <!-- loader Start -->
        <div id="loading">
            <div class="loader simple-loader">
                <div class="loader-body"></div>
            </div>
        </div>
        <!-- loader END -->
        <div class="wrapper">
            <section class="login-content">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <img src="{{asset('logo-sv.png')}}" alt="Logo Sekolah Vokasi" style="width: 100px; height: auto;">
                    </div>

                    <h2 class="mb-2 text-center">Selamat Datang</h2>
                    <p class="text-center">Login untuk menggunakan layanan akademik</p>

                    @error('email')
                        <div class="alert alert-danger" role="alert" style="margin-bottom: 0; padding: 0; text-align: center; border:none;">
                            <p>{{ $message }}</p>
                        </div>
                    @enderror

                    <form class="user" method="POST" action="{{ route('prosesLogin') }}">
                        @csrf
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" placeholder="Alamat Email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>
                        </div>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" placeholder="Kata Sandi"
                                class="form-control @error('email') is-invalid @enderror"
                                name="password" required autocomplete="current-password"
                                style="padding-right: 45px;">
                            <i class="fas fa-eye toggle-password" id="togglePassword" style="left: auto; right: 20px;"></i>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-masuk" name="login-button">
                                Masuk <i class="fas fa-arrow-right mr-1"></i>
                            </button>
                        </div>
                    </form>

                    <div class="divider">
                        <span>atau</span>
                    </div>

                    <p class="text-center mb-3">Mahasiswa silakan log in menggunakan email student.uns.ac.id</p>
                    <div class="d-grid">
                        <a class="btn btn-student-login" href="{{route('google.login')}}">
                            <i class="fas fa-envelope"></i>
                            Login Email Student UNS
                        </a>
                    </div>

                    <div class="copyright">
                        © 2024 Universitas Sebelas Maret. All rights reserved.
                    </div>
                </div>
            </section>
        </div>

        <!-- Library Bundle Script -->
        <script src="{{asset('back/assets/js/core/libs.min.js')}}"></script>

        <script>
            // Toggle password visibility
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        </script>

        <!-- External Library Bundle Script -->
        <script src="{{asset('back/assets/js/core/external.min.js')}}"></script>

        <!-- App Script -->
        <script src="{{asset('back/assets/js/hope-ui.js')}}" defer></script>
    </body>
</html>
