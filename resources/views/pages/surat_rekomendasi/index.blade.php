@extends('_template.master')

@section('title', 'Surat Rekomendasi')

@section('content')
<div class="position-relative">
    @include('_template.navbar')
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
    </div>

    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Surat Rekomendasi</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @can('mahasiswa')
                            <div class="d-flex justify-content-end pb-4 px-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Ajuan</button>
                            </div>
                            @include('pages.surat_rekomendasi.modal_tambah')
                            @include('pages.surat_rekomendasi.modal_edit')
                        @endcan
                        @canany(['staff','dekanat','subkoor','adminprodi','fo'])
                            @include('modals.proses')
                            @include('pages.surat_rekomendasi.modal_edit')
                        @endcanany
                        @include('pages.surat_rekomendasi.modal_detail')

                        <div class="table-responsive">
                            <table id="sr-datatable" class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        @can('mahasiswa')
                                            <th style="display:none;">Created At</th>
                                            <th>No</th>
                                            <th>Tanggal Lulus</th>
                                            <th>Nomor Ijazah</th>
                                            <th>Status</th>
                                            <th>Tanggal Submit</th>
                                            <th>Aksi</th>
                                        @endcan
                                        @canany(['staff','dekanat','subkoor','adminprodi','fo'])
                                            <th style="display:none;">Created At</th>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Program Studi</th>
                                            <th>Tanggal Lulus</th>
                                            <th>Nomor Ijazah</th>
                                            <th>Status</th>
                                            <th>Tanggal Submit</th>
                                            <th>Aksi</th>
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
    <script>
        // Set role-specific list endpoints so DataTables requests the JSON list instead of loading the HTML index route
        @can('mahasiswa')
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('suratRekomendasi.listMahasiswa'),
                'store' => route('suratRekomendasi.store'),
                'updateData' => route('suratRekomendasi.update', ':id'),
                'revisi' => route('suratRekomendasi.update', ':id'),
                'getData' => route('suratRekomendasi.show', ':id'),
                'deleteData' => route('suratRekomendasi.destroy', ':id'),
                'generate' => route('suratRekomendasi.generate', ':id'),
            ]) !!};
        @endcan
        @canany(['staff','dekanat','subkoor','adminprodi','fo'])
            window.Laravel = {!! json_encode([
                'baseUrl' => url('/'),
                'listData' => route('suratRekomendasi.listStaff'),
                'store' => route('suratRekomendasi.store'),
                'updateData' => route('suratRekomendasi.update', ':id'),
                'getData' => route('suratRekomendasi.show', ':id'),
                'deleteData' => route('suratRekomendasi.destroy', ':id'),
                'routeProses' => route('suratRekomendasi.proses', ':id'),
                'generate' => route('suratRekomendasi.generate', ':id'),
            ]) !!};
        @endcanany
    </script>
    @can('mahasiswa')
        <script src="{{ asset('custom/js/surat_rekomendasi/mahasiswa.js') }}"></script>
    @endcan
    @canany(['staff','dekanat','subkoor','adminprodi','fo'])
        <script src="{{ asset('custom/js/surat_rekomendasi/staff.js') }}"></script>
    @endcanany
@endpush
