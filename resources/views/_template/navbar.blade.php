@if (Auth::user()->prodi == '2' && !Request::is('user*'))
<div class="sticky-top">
@endif
<nav class="nav navbar sticky-top navbar-expand-lg navbar-light iq-navbar">
    <div class="container-fluid navbar-inner">
        <a href="{{route('home')}}" class="navbar-brand">
            <!--Logo start-->
            <img src="{{ asset('logosv.png') }}" height="40">
            <!--logo End-->
            <!--<h6 class="logo-title">{{ env('APP_NICKNAME') }}</h6>-->
        </a>

        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
            <svg width="20px" height="20px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
            </svg>
            </i>
        </div>

        <div class="input-group search-input">
            <span class="input-group-text" id="search-input">
                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
            <input type="search" class="form-control" id="searchInput" placeholder="Search...">
            <div class="dropdown search-dropdown list-group" id="searchDropdown">
                {{-- <ul class="list-group" aria-labelledby="search-input"> --}}
                    <!-- Isi dropdown akan diisi secara dinamis menggunakan JavaScript -->
                {{-- </ul> --}}
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <span class="navbar-toggler-bar bar1 mt-2"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto align-items-center navbar-list mb-2 mb-lg-0">
                @can('staff')
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link" id="mail-drop" data-bs-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-gear"></i>
                    </a>
                    <div class="sub-drop dropdown-menu dropdown-menu-end p-0" aria-labelledby="mail-drop">
                        <div class="card shadow-none m-0">
                            <div class="card-header d-flex justify-content-between bg-primary py-3">
                                <div class="header-title">
                                    <h5 class="mb-0 text-white">Setting</h5>
                                </div>
                            </div>
                            <div class="card-body p-0 ">
                                <a href="{{ editDekan() }}" class="iq-sub-card">
                                    <div class="d-flex  align-items-center">
                                        <div class="">
                                            <i class="fa fa-user-tie"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 ">Wakil Dekan 1</h6>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{route('user.index')}}" class="iq-sub-card">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <i class="fa fa-users"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 ">Users</h6>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{route('pembina.index')}}" class="iq-sub-card">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <i class="fa fa-user"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 ">Pembina</h6>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{route('prodi.index')}}" class="iq-sub-card">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <i class="fa fa-house-user"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 ">Prodi</h6>
                                        </div>
                                    </div>
                                </a>
                                {{-- <a href="{{route('suratHasil.index')}}" class="iq-sub-card">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <i class="fa fa-house-user"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 ">Template Surat Hasil</h6>
                                        </div>
                                    </div>
                                </a> --}}
                                <a href="{{route('layanan.index')}}" class="iq-sub-card">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <i class="fa fa-gears"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 ">Layanan</h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                @endcan

                <li class="nav-item dropdown">
                    <a class="nav-link py-0 d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ auth()->user()->foto ?? asset('back/assets/images/avatars/01.png')}}" alt="User-Profile" class="img-fluid avatar avatar-50 avatar-rounded">
                        <div class="caption ms-3 d-none d-md-block ">
                            <h6 class="mb-0 caption-title">{{ Str::limit(auth()->user()->name, 30) }}</h6>
                            <p class="mb-0 caption-sub-title">{{ auth()->user()->roles->name }}</p>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{route('user.edit', encodeId(Auth::user()->id))}}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{route('logout')}}">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
@if (Auth::user()->prodi == '2' && !Request::is('user*'))
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <svg width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.81409 20.4368H19.1971C20.7791 20.4368 21.7721 18.7267 20.9861 17.3527L13.8001 4.78775C13.0091 3.40475 11.0151 3.40375 10.2231 4.78675L3.02509 17.3518C2.23909 18.7258 3.23109 20.4368 4.81409 20.4368Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M12.0024 13.4147V10.3147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M11.995 16.5H12.005" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        <div>
            Silakan mengubah Program Studi Anda pada menu 
            <a href="{{route('user.edit', encodeId(Auth::user()->id))}}">Profil</a>
        </div>
    </div>
</div>
@endif