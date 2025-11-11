@extends('_template.master')

@section('title', 'Pemilu Mahasiswa')

@push('css')
<style>
    .pemilu-empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="position-relative">
    @include('_template.navbar')
    <div class="iq-navbar-header" style="height: 160px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1>Pemilu Mahasiswa</h1>
                            <p>Pilih calon terbaikmu untuk Presmben maupun Caleg.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{asset('back/assets/images/dashboard/top-header1.png')}}" alt="header" class="img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>
</div>

<div class="conatiner-fluid content-inner mt-n5 py-0">
    @if(!$pemilihanPresmben && !$pemilihanCaleg)
        <div class="card">
            <div class="pemilu-empty-state">
                <i class="fa-solid fa-person-booth fa-3x mb-3"></i>
                <h4>Belum ada pemilihan aktif</h4>
                <p>Pantau terus halaman ini ketika periode pemilihan dimulai.</p>
            </div>
        </div>
    @else
        <div class="row">
            @if($pemilihanPresmben)
                <div class="col-md-6 mb-4">
                    @include('pages.dashboard.partials.pemilihan-card', [
                        'jenis' => 'presmben',
                        'title' => 'Pemilihan Presmben',
                        'pemilihan' => $pemilihanPresmben
                    ])
                </div>
            @endif
            @if($pemilihanCaleg)
                <div class="col-md-6 mb-4">
                    @include('pages.dashboard.partials.pemilihan-card', [
                        'jenis' => 'caleg',
                        'title' => 'Pemilihan Caleg',
                        'pemilihan' => $pemilihanCaleg
                    ])
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('js')
<script src="{{ asset('custom/js/dashboard/pemilihan.js') }}?q{{Str::random(5)}}"></script>
@endpush
