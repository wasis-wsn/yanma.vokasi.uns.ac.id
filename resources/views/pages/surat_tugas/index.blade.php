@extends('_template.master')

@section('title', 'Surat Tugas')

@section('content')
<div class="position-relative">
    @include('_template.navbar')
    <!-- Nav Header Component Start -->
    <div class="iq-navbar-header" style="height: 80px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{asset('back/assets/images/dashboard/top-header1.png')}}" alt="header" class="img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>          <!-- Nav Header Component End -->
    <!--Nav End-->

    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Surat Tugas Delegasi</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <p>
                                {!! $layanan->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Tahapan Permohonan Surat Tugas Delegasi (Kegiatan Lomba)
                                <ol class="text-dark">
                                    <li>Klik tombol Tambah Ajuan</li>
                                    <li>Mengisi Form Permohonan</li>
                                    <li>
                                        Menggunggah berkas persyaratan sebagai berikut:
                                        <ol class="text-dark">
                                            <li>
                                                Jika mengajukan dana ke Sekolah Vokasi
                                                <ul class="text-dark">
                                                    <li>Surat Pengantar “Permohonan Delegasi” dari Program Studi</li>
                                                    <li>Surat “Permohonan Dana”</li>
                                                    <li>Lembar “Pernyataan LPJ dan SPJ”</li>
                                                    <li>ToR RAB Lomba</li>
                                                    <li>Pamflet/Undangan Lomba</li>
                                                </ul>
                                            </li>
                                            <li>
                                                Jika tidak mengajukan dana ke Sekolah Vokasi
                                                <ul class="text-dark">
                                                    <li>Surat Pengantar “ Permohonan Delegasi” dari Program Studi</li>
                                                    <li>Lembar “Pernyataan LPJ”</li>
                                                    <li>Pamflet/Undangan Lomba</li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </li>
                                </ol>
                            </p> --}}
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
                            {{-- <p class="text-dark">
                                Siapkan dokumen berikut:
                                <ul>
                                    <li>Surat Pengantar Prodi</li>
                                    <li>ToR RAB (jika mengajukan dana ke SV)</li>
                                    <li>Lembar Pernyataan LPJ/SPJ</li>
                                    <li>Pamflet/Undangan/Pengumuman/Pedoman Lomba</li>
                                </ul>
                            </p> --}}
                            <div class="d-flex justify-content-end pb-4 px-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                            </div>
                            @include('pages.surat_tugas.modal_tambah')
                            @include('pages.surat_tugas.modal_edit')
                        @endcan
                        @canany(['staff','dekanat','subkoor','adminprodi'])
                            <div class="d-flex justify-content-start pb-4">
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.proses')
                            @include('modals.export')
                            <div class="d-flex justify-content-end pb-4">
                                @can('adminprodi')
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="prodiDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Prodi</button>
                                    <ul class="dropdown-menu" aria-labelledby="prodiDropdown">
                                        <li><a class="dropdown-item prodi-menu" href="#" data-status="all">Semua</a></li>
                                        @foreach ($prodis as $prodi)
                                        <li><a class="dropdown-item prodi-menu" href="#" data-status="{{ $prodi->id }}">{{ $prodi->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endcan
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Semua</button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item status-menu" href="#" data-status="all">Semua</a></li>
                                        @foreach ($status as $st)
                                        <li><a class="dropdown-item status-menu" href="#" data-status="{{ $st->id }}">{{ $st->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahunDropdown" data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                    <ul class="dropdown-menu" aria-labelledby="tahunDropdown">
                                        @foreach ($tahuns as $tahun)
                                        <li><a class="dropdown-item tahun-menu" href="#" data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endcanany
                        @include('pages.surat_tugas.modal_detail')
                        
                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('mahasiswa')
                                            <th>No</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Nama Kegiatan</th>
                                            {{-- <th>Tempat</th> --}}
                                            <th>Waktu</th>
                                            <th>Status</th>
                                            <!-- <th>Antrian</th> -->
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                            @endcan
                                            @canany(['staff','dekanat','subkoor','adminprodi'])
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            @can('adminprodi')
                                            <th>Prodi</th>
                                            @endcan
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Waktu Kegiatan</th>
                                            <th>No Surat</th>
                                            <th>Status</th>
                                            <!-- <th>Antrian</th> -->
                                            <th>Catatan</th>
                                            @cannot('adminprodi')
                                                <th>Aksi</th>
                                            @endcannot
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody id="show_data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
    @can('mahasiswa')
        <script>
            const dospemInput = $('#dospem');
            const dospemDropdown = $('#listDospem');

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
            const delayedSearchDospem = debounce(function() {
                const keyword = dospemInput.val().toLowerCase();
                dospemDropdown.empty();

                if (keyword.trim() !== '') {
                    let filteredData = $.ajax({
                        url: `{{ route('get_dosen') }}?q=${keyword}`,
                        type: "GET"
                    });

                    filteredData.done(function(response) {
                        response.forEach(data => {
                            // const listItem = $('<a>').attr('href', '{{ env("APP_URL") }}' + data.url).addClass('list-group-item list-group-item-action').html(data.name);
                            const listItem = $('<li>').attr('data-dospem', data.name)
                                            .attr('data-nip_dospem', data.nip)
                                            .attr('data-nidn_dospem', data.nidn)
                                            .attr('data-unit_dospem', data.prodi.name)
                                            .addClass('list-group-item list-group-item-action')
                                            .html(data.name);
                            dospemDropdown.append(listItem);
                        });
                    });

                    dospemDropdown.show();
                } else {
                    dospemDropdown.hide();
                }
            }, 300); // Setel waktu penundaan sesuai kebutuhan (misalnya 300 ms)

            // Terapkan debounce pada input event
            dospemInput.on('input', delayedSearchDospem);

            $('#listDospem').on('click', '.list-group-item', function() {
                $('#dospem').val($(this).data('dospem'));
                $('#nip_dospem').val($(this).data('nip_dospem'));
                $('#nidn_dospem').val($(this).data('nidn_dospem'));
                $('#unit_dospem').val($(this).data('unit_dospem'));
                dospemDropdown.hide();
            });

            // $(document).click(function (event) {
            //     if (!$(event.target).closest('.search-input').length) {
            //         dospemDropdown.hide();
            //     }
            // });
        </script>
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('st.listMahasiswa'),
                'revisi' => route('st.revisi', ':id'),
                'getData' => route('st.show', ':id'),
                'deleteData' => route('st.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_tugas/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('st.export'),
                'listData' => route('st.listStaff'),
                'getData' => route('st.show', ':id'),
                'routeProses' => route('st.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_tugas/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('st.export'),
                'listData' => route('st.listDekanat'),
                'getData' => route('st.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_tugas/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany

    @can('adminprodi')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi') || "all";
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('st.export'),
                'listData' => route('st.listAdminProdi'),
                'getData' => route('st.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/surat_tugas/adminprodi.js') }}?q{{Str::random(5)}}"></script>
    @endcan
@endpush