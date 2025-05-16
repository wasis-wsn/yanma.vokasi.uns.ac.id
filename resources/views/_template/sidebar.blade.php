<aside class="sidebar sidebar-default navs-rounded-all ">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{route('home')}}" class="navbar-brand" width="100%">
            <!--Logo start-->
            <img src="{{ asset('logosv.png') }}" height="40">
            <!--<h3 class="logo-title" style="font-size: 20px;">{{ env('APP_NICKNAME') }}</h3>-->
            <!--logo End-->
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>
    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <!-- Sidebar Menu Start -->
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item nav-judul">
                    <a class="nav-link {{ Request::is('dashboard*') ? 'active' : ''}}" aria-current="page" href="{{ route('dashboard') }}">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z" fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item nav-judul">
                    <a class="nav-link {{ Request::is('user/edit*') ? 'active' : ''}}" aria-current="page" href="{{route('user.edit', encodeId(Auth::user()->id))}}">
                        <!--<i class="icon">-->
                        <!--    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                        <!--        <path opacity="0.4" d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z" fill="currentColor"></path>-->
                        <!--        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="currentColor"></path>-->
                        <!--    </svg>-->
                        <!--</i>-->
                        <i class="fa fa-user"></i>
                        <span class="item-name">Profil</span>
                    </a>
                </li>
                @can('staff')
                    <li class="nav-item nav-judul">
                        <a class="nav-link {{ Request::is('akreditasi*') ? 'active' : ''}}" aria-current="page" href="{{ route('akreditasi.index') }}">
                            <i class="fa-solid fa-certificate"></i>
                            <span class="item-name">Akreditasi</span>
                        </a>
                    </li>
                    <li class="nav-item nav-judul">
                        <a class="nav-link {{ Request::is('contact*') ? 'active' : ''}}" aria-current="page" href="{{ route('contact.index') }}">
                            <i class="fa fa-phone"></i>
                            <span class="item-name">Kontak</span>
                        </a>
                    </li>
                    <li class="nav-item nav-judul">
                        <a class="nav-link {{ Request::is('tpl*') ? 'active' : ''}}" aria-current="page" href="{{ route('template.index') }}">
                            <i class="fa fa-file"></i>
                            <span class="item-name">Template</span>
                        </a>
                    </li>
                    <li class="nav-item nav-judul">
                        <a class="nav-link {{ Request::is('berita*') ? 'active' : ''}}" aria-current="page" href="{{ route('berita.index') }}">
                            <i class="fa fa-newspaper-o"></i>
                            <span class="item-name">Berita</span>
                        </a>
                    </li>
                @endcan

                <li><hr class="hr-horizontal"></li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Layanan</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                {{-- getLayanan() bisa ditemukan di app/Helpers/Helper.php --}}
                @foreach (getLayanan() as $kategori)
                    <li class="nav-item nav-judul">
                        @if($kategori->name != 'Alumni' || auth()->user()->roles->gate_name != 'adminprodi')
                        @if($kategori->name != 'Kemahasiswaan' || auth()->user()->roles->gate_name != 'fo')
                        <a class="nav-link" data-bs-toggle="collapse" href="#{{$kategori->name}}-menu" role="button" aria-expanded="false" aria-controls="horizontal-menu">
                            {!! $kategori->icon !!}
                            <span class="item-name">{{$kategori->name}}</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        @endif
                        @endif
                        <ul class="sub-nav collapse" id="{{$kategori->name}}-menu" data-bs-parent="#sidebar-menu">
                        @foreach ($kategori->layanan as $layanan)
                            @if($layanan->name != 'Verifikasi Wisuda' || auth()->user()->roles->gate_name == 'staff')
                                @canany($layanan->gate)
                                    <li class="nav-item">
                                        <a class="nav-link {{ (Request::url() == $layanan->url_mhs || Request::url() == $layanan->url_staff) ? 'active' : ''}}"
                                            href="{{in_array(auth()->user()->roles->gate_name, ['mahasiswa','ormawa','adminprodi']) ? $layanan->url_mhs : $layanan->url_staff}}">
                                            <i class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                    <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <span class="item-name">{{$layanan->name}}</span>
                                        </a>
                                    </li>
                                @endcanany
                            @endif
                        @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
            <!-- Sidebar Menu End -->
        </div>
    </div>
    <div class="sidebar-footer"></div>
</aside>
