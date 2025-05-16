@extends('_template.master')

@section('title', 'Perpanjangan')

@push('css')
    <style>
        #detail-alasan, #detail-catatan {
            word-wrap: break-word;
            white-space: normal;
        }
    </style>
@endpush

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
                            <h4 class="card-title">Perpanjangan Studi</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <h5>
                                Jadwal Perpanjangan = {{\Carbon\Carbon::parse($perpanjangan->open_datetime)->translatedFormat('d F Y H:i:s')}} WIB - {{\Carbon\Carbon::parse($perpanjangan->close_datetime)->translatedFormat('d F Y H:i:s')}} WIB
                            </h5>
                            <p>
                                {!! $perpanjangan->keterangan !!}
                            </p>
                            {{-- <p class="text-dark">
                                Catatan :
                                <ul class="text-dark">
                                    <li>
                                        Pengajuan diluar jadwal ajuan harus mendatangi Akademik Sekolah Vokasi
                                    </li>
                                    <li>
                                        Surat Pengantar yang sudah ditanda tangani Wakil Dekan Akademik, Riset dan Kemahasiswaan 
                                        Sekolah Vokasi WAJIB diambil di Front Office Sekolah Vokasi
                                    </li>
                                    <li>
                                        Surat Pengantar yang sudah diambil, WAJIB diserahkan ke Akademik UNS (Pusat) oleh mahasiswa
                                    </li>
                                </ul>
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
                            @if ($tanggal >= $perpanjangan->open_datetime && $tanggal <= $perpanjangan->close_datetime)
                                <div class="d-flex justify-content-end pb-4 px-4">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                                </div>
                                @include('pages.perpanjangan.modal_tambah')
                            @endif
                            @include('pages.perpanjangan.modal_edit')
                        @endcan
                        @canany(['staff', 'dekanat', 'subkoor'])
                            <div class="d-flex justify-content-start pb-4">
                                @can('staff')
                                    <button type="button" class="btn btn-warning mx-2" data-bs-toggle="modal" data-bs-target="#modalJadwal">Ubah Jadwal</button>
                                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                                @endcan
                                <button type="button" class="btn btn-success mx-2" id="btn-export">Export Data</button>
                                <!-- Add bulk action button -->
                                <button type="button" class="btn btn-secondary mx-2" id="btn-bulk-action" disabled>
                                    <i class="fa fa-tasks"></i> Proses Terpilih
                                </button>
                            </div>
                            @include('modals.export_semester')
                            @can('staff')
                                @include('pages.perpanjangan.modal_tambah')
                                @include('pages.perpanjangan.modal_ubah_jadwal')
                            @endcan
                        @endcanany
                        @canany(['fo','staff'])
                            @include('modals.proses')
                        @endcanany
                        @cannot('mahasiswa')
                            <div class="d-flex justify-content-end pb-4">
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="prodiDropdown" data-bs-toggle="dropdown" data-prodi="all" aria-expanded="false">Prodi</button>
                                    <ul class="dropdown-menu" aria-labelledby="prodiDropdown">
                                        <li><a class="dropdown-item prodi-menu" href="#" data-prodi="all">Semua</a></li>
                                        @foreach ($prodis as $prodi)
                                        <li><a class="dropdown-item prodi-menu" href="#" data-prodi="{{ $prodi->id }}">{{ $prodi->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown mx-2">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" data-status="all" aria-expanded="false">Status</button>
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
                        @endcannot
                        
                        <div class="table-responsive">
                            <table id="suket-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('mahasiswa')
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Tanggal Submit</th>
                                            <th>Tanggal Proses</th>
                                            <!--<th>Tanggal Ambil</th>-->
                                            <th>Perpanjangan <br>Semester</th>
                                            <th>Perpanjangan <br>Ke</th>
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['staff', 'dekanat', 'subkoor'])
                                            <th hidden>created_at</th>
                                            <th><input type="checkbox" id="select-all" class="form-check-input"></th>
                                            <th>No</th>
                                            <th>Tanggal <br>Submit</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>No Surat</th>
                                            <th>Perpanjangan <br>Semester</th>
                                            <th>Perpanjangan <br>Ke</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        @endcanany
                                        @canany(['fo','adminprodi'])
                                            <th hidden>created_at</th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>Tanggal Ambil</th>
                                            <th>Aksi</th>
                                            <th>Catatan</th>
                                        @endcan
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

<!-- Modal section -->
@include('modals.export_semester')
@include('modals.proses')
@include('pages.perpanjangan.modal_detail')

<!-- Add bulk process modal here -->
<div class="modal fade" id="modalBulkProcess" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Data Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-bulk-process">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status_id" class="form-select" required>
                            <option value="">Pilih Status</option>
                            @foreach ($status as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" rows="3" class="form-control"></textarea>
                    </div>
                    <input type="hidden" name="selected_ids">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>

    @can('mahasiswa')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('perpanjanganStudi.listMahasiswa'),
                'revisi' => route('perpanjanganStudi.revisi', ':id'),
                'getData' => route('perpanjanganStudi.show', ':id'),
                'deleteData' => route('perpanjanganStudi.destroy', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/perpanjangan/mahasiswa.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('fo')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('perpanjanganStudi.listFo'),
                'update' => route('perpanjanganStudi.proses', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/perpanjangan/fo.js') }}?q{{Str::random(5)}}"></script>
    @endcan
    @can('staff')
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi');
            $(() => {
                $('#mahasiswa').select2({
                    dropdownParent: $('#div_mahasiswa'),
                    theme: 'bootstrap-5',
                    placeholder: "Pilih Mahasiswa",
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
                            return "Data Tidak Ditemukan. Pastikan Mahasiswa telah melakukan registrasi";
                        },
                        inputTooShort: function () {
                            return "Input minimal 3 huruf untuk memilih Mahasiswa";
                        },
                    },
                });
            })
        </script>

        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('perpanjanganStudi.export'),
                'listData' => route('perpanjanganStudi.listStaff'),
                'getData' => route('perpanjanganStudi.show', ':id'),
                'routeProses' => route('perpanjanganStudi.proses', ':id'),
                'bulkProcess' => route('perpanjanganStudi.bulkProcess'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/perpanjangan/staff.js') }}?q{{Str::random(5)}}"></script>
    @endcan

    @canany(['dekanat', 'subkoor'])
        <script>
            var year = $("#tahunDropdown").html();
            var status_table = $("#statusDropdown").data('status');
            var prodi_table = $("#prodiDropdown").data('prodi');
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('perpanjanganStudi.export'),
                'listData' => route('perpanjanganStudi.listDekanat'),
                'getData' => route('perpanjanganStudi.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/perpanjangan/dekanat.js') }}?q{{Str::random(5)}}"></script>
    @endcanany

    @can('adminprodi')
        <script>
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'export' => route('perpanjanganStudi.export'),
                'listData' => route('perpanjanganStudi.listAdminProdi'),
                'getData' => route('perpanjanganStudi.show', ':id'),
            ]) !!};
        </script>
        <script src="{{ asset('custom/js/perpanjangan/adminprodi.js') }}?q{{Str::random(5)}}"></script>
    @endcan
@endpush