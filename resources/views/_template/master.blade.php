
<!doctype html>
<html lang="id" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>@yield('title')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        
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

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


        <style>
            .search-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                z-index: 1000;
            }
            .sidebar-default .sidebar-body .sidebar-list .iq-main-menu .nav-item .nav-link .item-name {
                display: block;
                white-space: normal;
            }
            .sidebar-mini .sidebar-body .sidebar-list .iq-main-menu .nav-item .nav-link .item-name {
                display: block;
                white-space: nowrap;
            }
            .dropdown .dropdown-menu {
                max-height: 400px;
                overflow-y: auto;
                overflow-x: hidden;
                border: 1px solid #8A92A6;
                border-radius: .5rem;
            }
            .dropdown .dropdown-menu li:hover {
                background-color: rgb(216, 216, 216)
            }.dropdown-menu li:hover a {
                color: rgb(104, 104, 104);
            }
            .modal .modal-dialog .modal-content .modal-body{
                color: black;
            }
            .form-group .form-control, 
            .form-group .form-select{
                border: 1px solid #ced4da;
                color: rgb(33, 37, 41);
            }
        </style>

        @stack('css')
    </head>
    <body class="  ">
        <!-- loader Start -->
        <div id="loading">
            <div class="loader simple-loader">
                <div class="loader-body"></div>
            </div>    
        </div>
        <!-- loader END -->
        
        @include('_template.sidebar')

        <main class="main-content">
            @yield('content')
        </main>

        {{-- @include('_template.offcanvas') --}}

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
        
        <!-- AOS Animation Plugin-->
        <script src="{{asset('back/assets/vendor/aos/dist/aos.js')}}"></script>
        
        <!-- App Script -->
        <script src="{{asset('back/assets/js/hope-ui.js')}}" defer></script>

        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.45/moment-timezone.min.js"></script>
         --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

        <script>
            const listItems = document.querySelectorAll('.nav-judul');
            const lastListItem = listItems[listItems.length - 1];
            lastListItem.classList.add('mb-4');
        </script>

        <script>
                $(document).ready(function () {
                    const searchInput = $('#searchInput');
                    const searchDropdown = $('#searchDropdown');

                    // Definisikan fungsi debounce
                    function debounce(func, wait, immediate) {
                        var timeout;
                        return function() {
                            var context = this, args = arguments;
                            var later = function() {
                                timeout = null;
                                if (!immediate) func.apply(context, args);
                            };
                            var callNow = immediate && !timeout;
                            clearTimeout(timeout);
                            timeout = setTimeout(later, wait);
                            if (callNow) func.apply(context, args);
                        };
                    }

                    // Terapkan debounce pada fungsi input
                    const delayedSearch = debounce(function() {
                        const keyword = searchInput.val().toLowerCase();
                        searchDropdown.empty();

                        if (keyword.trim() !== '') {
                            let filteredData = $.ajax({
                                url: `{{ route('search') }}?q=${keyword}`,
                                type: "GET"
                            });

                            filteredData.done(function(response) {
                                response.forEach(data => {
                                    const listItem = $('<a>').attr('href', data.url).addClass('list-group-item list-group-item-action').html(data.name);
                                    searchDropdown.append(listItem);
                                });
                            });

                            searchDropdown.show();
                        } else {
                            searchDropdown.hide();
                        }
                    }, 300); // Setel waktu penundaan sesuai kebutuhan (misalnya 300 ms)

                    // Terapkan debounce pada input event
                    searchInput.on('input', delayedSearch);

                    $(document).click(function (event) {
                        if (!$(event.target).closest('.search-input').length) {
                            searchDropdown.hide();
                        }
                    });
                });
        </script>

        @stack('js')
    </body>
</html>