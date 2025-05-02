{{-- @dd($errors) --}}
<!doctype html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Registrasi</title>

            <!-- Favicon -->
            <link rel="shortcut icon" href="{{asset('logo-sv.png')}}" />
            
            <!-- Library / Plugin Css Build -->
            <link rel="stylesheet" href="{{asset('back/assets/css/core/libs.min.css')}}" />
            
            <!-- Aos Animation Css -->
            <link rel="stylesheet" href="{{asset('back/assets/vendor/aos/dist/aos.css')}}" />
            
            <!-- Hope Ui Design System Css -->
            <link rel="stylesheet" href="{{asset('back/assets/css/hope-ui.min.css?v=1.1.0')}}" />
            
            <!-- Custom Css -->
            <link rel="stylesheet" href="{{asset('back/assets/css/custom.min.css?v=1.1.0')}}" />
            
            <!-- Dark Css -->
            <link rel="stylesheet" href="{{asset('back/assets/css/dark.min.css')}}"/>
            
            <!-- RTL Css -->
            <link rel="stylesheet" href="{{asset('back/assets/css/rtl.min.css')}}"/>

            <style>
                .form-group .form-control, 
                .form-group .form-select{
                    border: 1px solid black;
                    color: rgb(33, 37, 41);
                }
            </style>
    </head>
    <body class="  ">
        <main class="main-content">
            <div class="position-relative mb-5">
                <!--Nav Start-->
                <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
                    <div class="container-fluid navbar-inner">
                        <a class="navbar-brand">
                            <h4 class="logo-title">Registrasi</h4>
                        </a>

                    </div>
                </nav>
            </div>
            <div class="conatiner content-inner mt-10 py-0">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Form Registrasi</h4>
                        </div>
                    </div>
                    <div class="card-body text-dark">
                        <p>Anda diharuskan untuk registrasi sebelum menggunakan Layanan Akademik Sekolah Vokasi UNS</p>
                        <form action="{{route('register')}}" method="POST">
                            @csrf
                            <input type="hidden" name="google_id" value="{{old('google_id', $user->id)}}" required>
                            <input type="hidden" name="foto" value="{{old('foto', $user->avatar)}}" required>
                            <input type="hidden" name="email" value="{{old('email', $user->email)}}" required>
                            <div class="form-group">
                                <label for="Custom01" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" id="Custom01" name="name" value="{{old('name', $user->name)}}" required>
                                @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{$errors->first('name')}}
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="Custom04" class="form-label">Program Studi</label>
                                <select class="form-select @if($errors->has('prodi')) is-invalid @endif" id="Custom04" name="prodi" required>
                                    @foreach ($prodis as $prodi)
                                    <option value="{{$prodi->id}}" @selected(old('prodi') == $prodi->id)>{{$prodi->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('prodi'))
                                <div class="invalid-feedback">
                                    {{$errors->first('prodi')}}
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="Custom05" class="form-label">NIM</label>
                                <input type="text" class="form-control @if($errors->has('nim')) is-invalid @endif" id="Custom05" name="nim" value="{{old('nim')}}" required>
                                @if ($errors->has('nim'))
                                <div class="invalid-feedback">
                                    {{$errors->first('nim')}}
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="no_wa" class="form-label">Nomor WhatsApp (aktif)</label>
                                <input type="text" class="form-control @if($errors->has('no_wa')) is-invalid @endif" id="no_wa" name="no_wa" value="{{old('no_wa')}}" required>
                                @if ($errors->has('no_wa'))
                                <div class="invalid-feedback">
                                    {{$errors->first('no_wa')}}
                                </div>
                                @endif
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>

        <!-- Library Bundle Script -->
        <script src="{{asset('back/assets/js/core/libs.min.js')}}"></script>
            
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
        
        <!-- AOS Animation Plugin-->
        
        <!-- App Script -->
        <script src="{{asset('back/assets/js/hope-ui.js')}}" defer></script>
    </body>
</html>