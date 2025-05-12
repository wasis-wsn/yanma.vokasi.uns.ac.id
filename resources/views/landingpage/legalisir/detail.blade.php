@extends('landingpage.template')

@push('css')
<style>
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

    <main id="main" class="my-4">
        <div class="container pt-4">
            <div class="input-group border border-dark">
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