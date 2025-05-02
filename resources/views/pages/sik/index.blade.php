@extends('_template.master')

@section('title', 'SIK')

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
                            <h4 class="card-title">Surat Izin Kegiatan</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('ormawa')
                            <p>
                                {!! $layanan->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Saat Anda mengajukan Surat Izin Kegiatan, Anda wajib mengunggah Proposal Kegiatan dan Lampiran.
                                <br>
                                Untuk Lampiran terdiri dari:
                                <ul class="text-dark">
                                    <li>Surat “Permohonan Izin Kegiatan”</li>
                                    <li>Form “Analisa Resiko Kegiatan”</li>
                                    <li>Lembar “Pengesahan Kegiatan”</li>
                                    <li>Lembar “Pengesahan Dana” (jika menggunakan dana)</li>
                                    <li>Lembar “Pemohonan Dana” (jika menggunakan dana)</li>
                                    <li>Lembar Penyataan tentang “LPJ dan SPJ” Kegiatan</li>
                                </ul>
                            </p>
                            <p class="text-dark">
                                Mohon diperhatikan:
                                <ol class="text-dark">
                                    <li>Proposal dan Laporan Pertanggungjawaban Kegiatan (LPJ) dibuat dengan ukuran kertas kuarto dan di sisi kanan atas disediakan kolom Nomor dan Tahun.</li>
                                    <li>LPJ dan SPJ diserahkan sesuai dengan tanggal yang ada di Lembar Pernyataan.</li>
                                    <li>Format lampiran sudah disediakan silahkan diisi dan tidak dirubah bentuknya.</li>
                                    <li>Lampiran Surat Permohonan Izin Kegiatan menggunakan KOP Resmi Ormawa</li>
                                    <li>Permohonan Izin Kegiatan diajukan maksimal satu minggu sebelum kegiatan dilaksanakan.</li>
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
                            <div class="d-flex justify-content-end pb-4 px-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                            </div>
                            @include('pages.sik.modal_tambah')
                            @include('pages.sik.modal_edit')
                        @endcan
                        @canany(['staff','dekanat','subkoor','adminprodi'])
                            <div class="d-flex justify-content-start pb-4">
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                            </div>
                            @include('modals.proses')
                            @include('modals.export')
                            <div class="d-flex justify-content-end pb-4">
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
                        @include('pages.sik.modal_detail')
                        
                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('ormawa')
                                            <th>No</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Ketua Kegiatan</th>
                                            <th>Tempat</th>
                                            <th>Waktu</th>
                                            <th>Jenis</th>
                                            <th>Status</th>
                                            <!-- <th>Antrian</th> -->
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        @endcan
                                            @canany(['staff','dekanat','subkoor','adminprodi'])
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <th>Nama Ormawa</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Tempat</th>
                                            <th>Waktu</th>
                                            <th>Jenis</th>
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
    @if (session('error'))
        <script>
            toastr.error("{{session('error')}}")
        </script>
    @endif

    @can('ormawa')
        <script>
            $().ready(function() {
                $('#ketua_id').select2({
                    dropdownParent: $('#div_ketua_id'),
                    theme: 'bootstrap-5',
                    placeholder: "Pilih Ketua",
                    ajax: {
                        delay: 400,
                        url: "{{route('get_mhs')}}",
                        type: "GET",
                        dataType: "json",
                        data: function (params) {
                            return {
                                q: $.trim(params.term),
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.nim + ' - ' + item.name + ' - ' + item.prodis.name,
                                        id: item.id
                                    }
                                })
                            };
                        },
                        escapeMarkup: function(markup) {
                            return markup;
                        }
                    },
                    minimumInputLength: 3,
                    language: {
                        noResults: function() {
                            return "Data Tidak Ditemukan. Pastikan Ketua Anda telah melakukan registrasi";
                        },
                        inputTooShort: function () {
                            return "Input minimal 3 huruf untuk memilih Ketua";
                        },
                    },
                });

                $('#edit_ketua_id').select2({
                    dropdownParent: $('#div_edit_ketua_id'),
                    theme: 'bootstrap-5',
                    placeholder: "Pilih Ketua",
                    ajax: {
                        delay: 400,
                        url: "{{route('get_mhs')}}",
                        type: "GET",
                        dataType: "json",
                        data: function (params) {
                            return {
                                q: $.trim(params.term),
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.nim + ' - ' + item.name + ' - ' + item.prodis.name,
                                        id: item.id
                                    }
                                })
                            };
                        },
                    },
                    minimumInputLength: 3,
                    language: {
                        noResults: function() {
                            return "Data Tidak Ditemukan. Pastikan Ketua Anda telah melakukan registrasi";
                        },
                        inputTooShort: function () {
                            return "Input minimal 3 huruf untuk memilih Ketua";
                        },
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
            });

            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('sik.listOrmawa'),
                'revisi' => route('sik.revisi', ':id'),
                'getData' => route('sik.show', ':id'),
                'deleteData' => route('sik.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/sik/ormawa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('sik.export'),
                'listData' => route('sik.listStaff'),
                'getData' => route('sik.show', ':id'),
                'routeProses' => route('sik.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/sik/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat','subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('sik.export'),
                'listData' => route('sik.listDekanat'),
                'getData' => route('sik.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/sik/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany

    @can('adminprodi')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('sik.export'),
                'listData' => route('sik.listAdminProdi'),
                'getData' => route('sik.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/sik/adminprodi.js') }}?q{{Str::random(5)}}"></script>
    @endcanany
@endpush