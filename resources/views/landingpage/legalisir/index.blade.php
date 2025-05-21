@extends('landingpage.template')

@push('css')
<style>
    #main {
        background: url("{{ asset('landingpage/assets/img/bg-white-3.png') }}") 0 0 repeat;
    }
    .search-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                z-index: 1000;
            }
</style>
    
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">

            <h2>Legalisir</h2>
            <p>
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('legalisir.landingPage')}}" class="text-white">Legalisir</a>
            </p>
        </div>
    </section>

    <main id="main">
        <div class="container pt-4">
            <div class="input-group border border-dark">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-end-0" style="height: 100%"><i class="bi bi-search"></i></span>
                </div>
                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari ajuan legalisir berdasarkan Nama dan NIM">
                <div class="dropdown search-dropdown list-group" id="searchDropdown">
                </div>
            </div>
        </div>
        <section>
            <div class="container">
                <h3>Legalisir</h3>
                <h5>Alur Legalisir</h5>
                <ol>
                    <li>Alumni menyiapkan dokumen yang akan dilegalisir.</li>
                    <li>Dokumen dimasukkan dalam <b>Map Kertas</b>. Bagian depan diberi keterangan: <b>Legalisir, Nama, NIM, Prodi, No WhatsApp, Keperluan Legalisir</b></li>
                    <li>Dokumen dikumpulkan di Front Office Sekolah Vokasi UNS.</li>
                    <li>Cek status ajuan legalisir melalui : <a href="{{route('legalisir.landingPage')}}">{{route('legalisir.landingPage')}}</a></li>
                    <li>
                        Alumni mengambil dokumen ke Sekolah Vokasi dan mengisi buku pengambilan dokumen. Jika diwakilkan maka harus membuat Surat Kuasa.
                        <br>
                        (unduh surat kuasa : <a href="https://drive.google.com/file/d/1EGYofWatVauc5TmybcIR3CVML5u6xy2v/view?usp=sharing">Surat Kuasa</a>)
                    </li>
                    <li>Selesai</li>
                </ol>
                @if (count($templates) > 0)
                <p class="text-dark">
                    Template File:
                    <ul class="text-dark">
                        @foreach ($templates as $item)
                            <li>
                                {{$item->template}}
                                (<a href="{{asset('storage/template/'.$item->file)}}">download</a>)
                            </li>
                        @endforeach
                    </ul>
                </p>
                @endif
                <h5>Ketentuan Legalisir</h5>
                <ol>
                    <li>
                        Foto kopi bisa <b>terbaca</b> dan <b>terlihat jelas</b>
                        <ul>
                            <li>Nama Yang bersangkutan</li>
                            <li>Nomor Ijazah/Transkrip</li>
                            <li>Foto</li>
                            <li>Nama, NIP, dan Tandatangan Pejabat</li>
                            <li>Logo UNS</li>
                            <li>Stempel Pengesahan</li>
                            <li>Tabel Mata Kuliah</li>
                            <li>Tabel Tidak Miring</li>
                            <li>Kop Tidak Terpotong</li>
                            <li>Angka atau Huruf Tidak Terpotong</li>
                        </ul>
                    </li>
                    <li>
                        Ukuran Kertas
                        <ul>
                            <li>
                                Untuk Fotokopi Ijazah = Ukuran kertas <b>A4</b> sesuai dokumen asli
                            </li>
                            <li>
                                Untuk Fotokopi Transkrip Akademik = Ukuran kertas <b>F4</b> sesuai dokumen asli
                            </li>
                            <li>
                                Untuk Fotokopi Akreditasi = Ukuran kertas <b>A4</b> sesuai dokumen asli
                            </li>
                            <li>
                                Untuk Fotokopi Lainnya = Ukuran kertas sesuai dokumen asli
                            </li>
                        </ul>
                    </li>
                    <li>Jumlah maksimal legalisir yaitu 10 lembar (keseluruhan).</li>
                    <li>Berkas akan diambil masing-masing 1 lembar untuk diarsipkan Sekolah Vokasi.</li>
                </ol>
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