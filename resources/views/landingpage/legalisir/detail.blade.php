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

    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1000;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    /* Table styling */
    .detail-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background-color: rgba(var(--color-secondary-rgb), 0.03);
        font-weight: 600;
        color: #1e293b;
        padding: 16px 20px;
    }

    .table td {
        padding: 16px 20px;
        color: #475569;
    }

    .table tr:not(:last-child) {
        border-bottom: 1px solid #f1f5f9;
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

            <h2 class="text-white fw-bold mb-3" data-aos="fade-up">Legalisir</h2>
            <p class="text-white" data-aos="fade-up" data-aos-delay="100">
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('legalisir.landingPage')}}" class="text-white">Legalisir</a>
            </p>
        </div>
    </section>

    <main id="main">
        <div class="container pt-4" data-aos="fade-up">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-transparent border-end-0" style="height: 100%"><i class="bi bi-search"></i></span>
                </div>
                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari ajuan legalisir berdasarkan Nama dan NIM">
                <div class="dropdown search-dropdown list-group" id="searchDropdown">
                </div>
            </div>
        </div>
        <section>
            <div class="container">
                <div class="detail-card" data-aos="fade-up" data-aos-delay="200">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th width="30%">Tanggal Mengajukan</th>
                                <td width="70%">: {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y H:i:s') }} WIB</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>: {{ $data->name }}</td>
                            </tr>
                            <tr>
                                <th>NIM</th>
                                <td>: {{ $data->nim }}</td>
                            </tr>
                            <tr>
                                <th>Prodi</th>
                                <td>: {{ $data->prodi->name }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>: <button class="btn {{ $data->status->color }} btn-sm" disabled>{{ $data->status->name }}</button></td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td>: {{ $data->catatan }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal pengambilan</th>
                                <td>: {{ ($data->tanggal_ambil) ? \Carbon\Carbon::parse($data->tanggal_ambil)->translatedFormat('d F Y H:i:s').' WIB' : '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('js')
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
                    url: `{{ route('legalisir.search') }}?q=${keyword}`,
                    type: "GET"
                });
                filteredData.done(function(response) {
                    response.forEach(data => {
                        const listItem = $('<a>').attr('href', '{{ route("legalisir.detail") }}?id=' + data.id)
                                            .addClass('list-group-item list-group-item-action text-center')
                                            .html(data.nim + ' - ' + data.name + ' - ' + data.prodi.name);
                        searchDropdown.append(listItem);
                    });
                });
                searchDropdown.show();
            } else {
                searchDropdown.hide();
            }
        }, 500);
        // Terapkan debounce pada input event
        searchInput.on('input', delayedSearch);
        $(document).click(function (event) {
            if (!$(event.target).closest('.search-input').length) {
                searchDropdown.hide();
            }
        });
    });
</script>
@endpush
