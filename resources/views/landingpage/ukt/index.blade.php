@extends('landingpage.template')

@push('css')
<style>
    #main {
        background: url("{{ asset('landingpage/assets/img/bg-white-3.png') }}") 0 0 repeat;
    }
    .jenis-ukt {
        font-size: -webkit-xxx-large;
        font-weight: bold;
    }
    .tgl-ukt {
        font-weight: bold;
        text-decoration: underline;
    }
    .teks-arial {
        font-family: Arial, sans-serif;
    }
</style>
    
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">

            <h2>Keringanan UKT</h2>
            <p>
                <a href="{{route('home')}}" class="text-white">Home</a> / <a href="{{route('ukt.landingPage')}}" class="text-white">Keringanan UKT</a>
            </p>
        </div>
    </section>

    <main id="main">
        @foreach ($ukt as $item)
        <div class="container py-4">
            <h1 class="text-center jenis-ukt teks-arial">Keringanan UKT <br> ({{$item['jenis']}})</h1>
            <p>
                {!! $item['keterangan'] !!}
            </p>
            <br>
            <h4 class="text-center teks-arial">Jadwal Pengajuan Keringanan UKT oleh Mahasiswa dimulai tanggal :</h4>
            <h4 class="text-center tgl-ukt">{{ $item['pengajuan'] }}</h4>
            <h4 class="text-center teks-arial">Jadwal Verifikasi Keringanan UKT oleh Fakultas dimulai tanggal :</h4>
            <h4 class="text-center tgl-ukt">{{ $item['verif_fakultas'] }}</h4>
            <h4 class="text-center teks-arial">Jadwal Verifikasi Keringanan UKT oleh Universitas dimulai tanggal :</h4>
            <h4 class="text-center tgl-ukt">{{ $item['verif_univ'] }}</h4>
            <br>
            <h2 class="text-center teks-arial"><b>Persyaratan</b></h2>
            <p>
                {!! $item['persyaratan'] !!}
            </p>
        </div>
        @endforeach
    </main>
@endsection