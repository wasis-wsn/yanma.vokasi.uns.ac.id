@extends('landingpage.template')

@push('css')
<style>
    .faq {
        background: url("{{ asset('landingpage/assets/img/bg-white-3.png') }}") 0 0 repeat;
    }
</style>
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">

            <h2>Akreditasi</h2>
            <p>
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('akreditasi.landingPage')}}" class="text-white">Akreditasi</a>
            </p>
        </div>
    </section>

    <main id="main">
        <section id="faq" class="faq">
            <div class="container-fluid" data-aos="fade-up">
                <div class="section-header">
                    <h2 class="text-dark">Gunakan Dokumen Akreditasi sesuai dengan tahun kelulusan</h2>
                </div>

                <div class="container">
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
                    <div class="col d-flex flex-column justify-content-center align-items-stretch  order-2 order-lg-1">
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