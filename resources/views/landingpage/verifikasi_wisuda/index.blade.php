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

            <h2>Verifikasi Wisuda</h2>
            <p>
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="#" class="text-white">Verifikasi Wisuda</a>
            </p>
        </div>
    </section>

    <main id="main">
        <section>
            <div class="container">
                <h3>Verifikasi Wisuda</h3>
                <h5>Alur Baru Verifikasi Wisuda Sekolah Vokasi UNS</h5>
                <ol>
                    <li><b>Hanya mahasiswa yang Surat Keterangan Lulus (SKL)-nya sudah terbit dan nomor ijazahnya sudah keluar</b> yang bisa mengajukan berkas verifikasi wisuda.</li>
                    <li><b>Upload berkas verifikasi wisuda dan verifikasi wisuda hanya bisa dilakukan pada saat jadwal verifikasi wisuda sudah dibuka.</b></li>
                    <li>Data akan muncul saat Akademik Sekolah Vokasi melakukan import data mahasiswa.<b> Data tersebut berdasarkan update nomor ijazah dari Akademik Pusat, bukan ditentukan oleh Akademik Sekolah Vokasi.</b></li>
                    <li>Log in ke web <a href="https://yanma.vokasi.uns.ac.id/login" target="_blank">https://yanma.vokasi.uns.ac.id/</a> menggunakan email SSO.</li>
                    <li>Pilih menu "Verifikasi Wisuda".</li>
                    <li>Upload berkas persyaratan verifikasi wisuda.</li>
                    <li><b>Mohon ditunggu dan cek secara berkala</b> di web yanma untuk <b>validasi dokumen</b> oleh Akademik Sekolah Vokasi.</li>
                    <li>Jika berkas sudah divalidasi oleh Akademik Sekolah Vokasi, mahasiswa bisa <b>segera melakukan konfirmasi kehadiran wisuda.</b></li>
                    <li>Mahasiswa yang memilih konfirmasi kehadiran <b>"Bersedia"</b> ajuan akan diverifikasi oleh Akademik Sekolah Vokasi untuk mengikuti wisuda pada periode tersebut.</li>
                    <li>Mahasiswa yang memilih konfirmasi kehadiran <b>"Tidak Bersedia"</b> ajuan tidak akan diverifikasi untuk wisuda periode tersebut dan <b>akan masuk dalam antrian verifikasi wisuda pada periode berikutnya.</b></li>
                    <li>Status verifikasi dapat dilihat diweb yanma atau bisa juga cek berkala di web <a href="https://wisuda.uns.ac.id/" target="_blank">https://wisuda.uns.ac.id/</a> (log in web wisuda > pilih menu sinkron data > scroll kebawah, cari kolom "Pin Kode Akses Wisuda"), <b>jika sudah ada angkanya berarti sudah diverifikasi oleh Akademik Sekolah Vokasi.</b></li>
                    <li>Jika sudah diverifikasi, <b>cek data pada menu sinkron data dan silahkan segera melakukan sinkronisasi data.</b></li>
                    <li><b>Cetak draft ijazah</b> diweb wisuda pada menu Cetak > Draft Ijazah.</li>
                    <li>Bagi mahasiswa <b>yang sudah diverifikasi tidak bisa mengundurkan diri</b> dengan alasan apapun.</li>
                    <li><b>Wajib bergabung digrup telegram wisuda</b> (link grup ada dihalaman awal web wisuda).</li>
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