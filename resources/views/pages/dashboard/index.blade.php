@extends('_template.master')

@section('title', 'Dashboard')

@pushIf(auth()->user()->role == 3, 'css')
<style>
    .list-legend{
        max-height: 400px;
        overflow-y: auto;
        overflow-x: auto;
    }
    .list-legend::-webkit-scrollbar {
        width: 12px;
    }
</style>
<link rel="stylesheet" href="https://code.highcharts.com/dashboards/css/dashboards.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endPushIf

@section('content')
<div class="position-relative">
    @include('_template.navbar')
    <!-- Nav Header Component Start -->
    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1>Halo {{ auth()->user()->name }}!</h1>
                            <p>Silahkan pilih layanan pada menu disamping atau pada pilihan dibawah.</p>
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
</div>
<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-md-12">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 mb-3">
                @foreach (getLayanan() as $kategori)
                    <div class="col">
                        <div class="card mb-4 rounded-3 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title pricing-card-title text-center">{{$kategori->name}}</h3>
                                <ul class="list-unstyled my-3">
                                    @foreach ($kategori->layanan as $layanan)
                                        @if($layanan->name != 'Verifikasi Wisuda')
                                            @canany($layanan->gate)
                                                <li>
                                                    <a href="{{in_array(auth()->user()->roles->gate_name, ['mahasiswa','ormawa']) ? $layanan->url_mhs : $layanan->url_staff}}">
                                                        {{$layanan->name}}
                                                    </a>
                                                </li>
                                            @endcanany
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@can('dekanat')
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-xl">
                    <div class="card">
                        <div class="d-flex justify-content-end px-4 pt-2 text-dark">
                            <p>Tahun</p>
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahunDiluarJadwal" data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                <ul class="dropdown-menu" aria-labelledby="tahunDiluarJadwal">
                                    @foreach ($tahuns as $tahun)
                                    <li><a class="dropdown-item tahun-diluar-jadwal" data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>  
                        <div class="card-body p-0 pb-2">
                            <div class="w-100">
                                <canvas id="grafik-diluarjadwal" style="max-height: 500px"></canvas>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Legend Prodi</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-legend" id="list-diluar-jadwal">
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-xl">
                    <div class="card">
                        <div class="d-flex justify-content-end px-4 pt-2 text-dark">
                            <p>Tahun</p>
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahunCuti" data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                <ul class="dropdown-menu" aria-labelledby="tahunCuti">
                                    @foreach ($tahuns as $tahun)
                                    <li><a class="dropdown-item tahun-cuti" href="#" data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>  
                        <div class="card-body p-0 pb-2">
                            <div class="w-100">
                                <canvas id="grafik-cuti" style="max-height: 500px"></canvas>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Legend Prodi</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-legend" id="list-cuti">
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-xl">
                    <div class="card">
                        <div class="d-flex justify-content-end px-4 pt-2 text-dark">
                            <p>Tahun</p>
                            <div class="dropdown mx-2">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="tahunSuratTugas" data-bs-toggle="dropdown" aria-expanded="false">{{ date('Y') }}</button>
                                <ul class="dropdown-menu" aria-labelledby="tahunSuratTugas">
                                    @foreach ($tahuns as $tahun)
                                    <li><a class="dropdown-item tahun-surat-tugas" href="#" data-year="{{ $tahun->tahun }}">{{ $tahun->tahun }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>  
                        <div class="card-body p-0 pb-2">
                            <div class="w-100">
                                <canvas id="grafik-surattugas" style="max-height: 500px"></canvas>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Legend Prodi</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-legend" id="list-surattugas">
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
</div>
@endsection

@pushIf(auth()->user()->role == 3, 'js')
    <script>
        let tahunDiluarJadwal = $('#tahunDiluarJadwal').html();
        let chartDiluarJadwal;
        $(() => {
            createChartsDiluarJadwal(tahunDiluarJadwal);
        });
        $(".tahun-diluar-jadwal").click(function () {
            tahunDiluarJadwal = $(this).data("year");
            $("#tahunDiluarJadwal").html(tahunDiluarJadwal);
            updateChartsDiluarJadwal(tahunDiluarJadwal);
        });
    
        function createChartsDiluarJadwal(tahun) {
            let url = "{{ route('grafik.diluarjadwal', ':tahun') }}";
            url = url.replace(':tahun', tahun);
            $.ajax({
                url: url,
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType: 'json',
                success: (res) => {
                    initialChartDiluarJadwal(res);
                }
            })
        }
    
        function initialChartDiluarJadwal(data) {
            let keyProdi = Object.keys(data.nama_prodi);
            let legendProdi = '';
            Object.keys(data.nama_prodi).forEach(key => {
                legendProdi += `<li>
                        ${key}. 
                        ${data.nama_prodi[key].nama_prodi} 
                        (${data.nama_prodi[key].jumlah_data})
                    </li>`;
            });
            $('#list-diluar-jadwal').html(legendProdi);
    
            let ctx = document.getElementById('grafik-diluarjadwal').getContext('2d');
            chartDiluarJadwal = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: keyProdi,
                    datasets: [{
                        label: 'Jumlah Ajuan',
                        data: data.jumlah_data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Grafik Ajuan Pembayaran UKT Diluar Jadwal Tahun ' + tahunDiluarJadwal,
                            align: 'center'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Program Studi'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Ajuan'
                            }
                        }
                    }
                }
            });
        }
    
        function updateChartsDiluarJadwal(tahun) {
            let url = "{{ route('grafik.diluarjadwal', ':tahun') }}";
            url = url.replace(':tahun', tahun);
            $.ajax({
                url: url,
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType: 'json',
                success: (res) => {
                    let keyProdi = Object.keys(res.nama_prodi);
                    let legendProdi = '';
                    Object.keys(res.nama_prodi).forEach(key => {
                        legendProdi += `<li>
                                ${key}. 
                                ${res.nama_prodi[key].nama_prodi} 
                                (${res.nama_prodi[key].jumlah_data})
                            </li>`;
                    });
                    $('#list-diluar-jadwal').html(legendProdi);
                    chartDiluarJadwal.data.labels = keyProdi;
                    chartDiluarJadwal.data.datasets[0].data = res.jumlah_data;
                    chartDiluarJadwal.options.plugins.title.text = 'Grafik Ajuan Pembayaran UKT Diluar Jadwal Tahun ' + tahunDiluarJadwal;
                    chartDiluarJadwal.update();
                }
            })
        }
    </script>
    <script>
        let tahunCuti = $('#tahunCuti').html();
        let chartCuti;

        $(() => {
            createChartsCuti(tahunCuti);
        });

        $(".tahun-cuti").click(function () {
            tahunCuti = $(this).data("year");
            $("#tahunCuti").html(tahunCuti);
            updateChartsCuti(tahunCuti);
        });

        function createChartsCuti(tahun) {
            let url = "{{ route('grafik.cuti', ':tahun') }}";
            url = url.replace(':tahun', tahun);
            $.ajax({
                url: url,
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType: 'json',
                success: (res) => {
                    initialChartCuti(res);
                }
            });
        }

        function initialChartCuti(data) {
            let keyProdi = Object.keys(data.nama_prodi);
            let legendProdi = '';
            Object.keys(data.nama_prodi).forEach(key => {
                legendProdi += `<li>
                        ${key}. 
                        ${data.nama_prodi[key].nama_prodi} 
                        (${data.nama_prodi[key].jumlah_data})
                    </li>`;
            });
            $('#list-cuti').html(legendProdi);

            let ctx = document.getElementById('grafik-cuti').getContext('2d');
            chartCuti = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: keyProdi,
                    datasets: [{
                        label: 'Jumlah Ajuan',
                        data: data.jumlah_data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Grafik Ajuan Selang/Cuti Tahun ' + tahunCuti,
                            align: 'center'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Program Studi'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Ajuan'
                            }
                        }
                    }
                }
            });
        }

        function updateChartsCuti(tahun) {
            let url = "{{ route('grafik.cuti', ':tahun') }}";
            url = url.replace(':tahun', tahun);
            $.ajax({
                url: url,
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType: 'json',
                success: (res) => {
                    let keyProdi = Object.keys(res.nama_prodi);
                    let legendProdi = '';
                    Object.keys(res.nama_prodi).forEach(key => {
                        legendProdi += `<li>
                                ${key}. 
                                ${res.nama_prodi[key].nama_prodi} 
                                (${res.nama_prodi[key].jumlah_data})
                            </li>`;
                    });
                    $('#list-cuti').html(legendProdi);

                    chartCuti.data.labels = keyProdi;
                    chartCuti.data.datasets[0].data = res.jumlah_data;
                    chartCuti.options.plugins.title.text = 'Grafik Ajuan Selang/Cuti Tahun ' + tahunCuti;
                    chartCuti.update();
                }
            });
        }
    </script>
    <script>
        let tahunSuratTugas = $('#tahunSuratTugas').html();
        let chartSuratTugas;

        $(() => {
            createChartsSuratTugas(tahunSuratTugas);
        });

        $(".tahun-surat-tugas").click(function () {
            tahunSuratTugas = $(this).data("year");
            $("#tahunSuratTugas").html(tahunSuratTugas);
            updateChartsSuratTugas(tahunSuratTugas);
        });

        function createChartsSuratTugas(tahun) {
            let url = "{{ route('grafik.surattugas', ':tahun') }}";
            url = url.replace(':tahun', tahun);
            $.ajax({
                url: url,
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType: 'json',
                success: (res) => {
                    initialChartSuratTugas(res);
                }
            });
        }

        function initialChartSuratTugas(data) {
            let keyProdi = Object.keys(data.nama_prodi);
            let legendProdi = '';
            Object.keys(data.nama_prodi).forEach(key => {
                legendProdi += `<li>
                        ${key}. 
                        ${data.nama_prodi[key].nama_prodi} 
                        (${data.nama_prodi[key].jumlah_data})
                    </li>`;
            });
            $('#list-surattugas').html(legendProdi);

            let ctx = document.getElementById('grafik-surattugas').getContext('2d');
            chartSuratTugas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: keyProdi,
                    datasets: [{
                        label: 'Jumlah Ajuan',
                        data: data.jumlah_data,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Grafik Ajuan Surat Tugas Delegasi Tahun ' + tahunSuratTugas,
                            align: 'center'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Program Studi'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Ajuan'
                            }
                        }
                    }
                }
            });
        }

        function updateChartsSuratTugas(tahun) {
            let url = "{{ route('grafik.surattugas', ':tahun') }}";
            url = url.replace(':tahun', tahun);
            $.ajax({
                url: url,
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                dataType: 'json',
                success: (res) => {
                    let keyProdi = Object.keys(res.nama_prodi);
                    let legendProdi = '';
                    Object.keys(res.nama_prodi).forEach(key => {
                        legendProdi += `<li>
                                ${key}. 
                                ${res.nama_prodi[key].nama_prodi} 
                                (${res.nama_prodi[key].jumlah_data})
                            </li>`;
                    });
                    $('#list-surattugas').html(legendProdi);

                    chartSuratTugas.data.labels = keyProdi;
                    chartSuratTugas.data.datasets[0].data = res.jumlah_data;
                    chartSuratTugas.options.plugins.title.text = 'Grafik Ajuan Surat Tugas Delegasi Tahun ' + tahun;
                    chartSuratTugas.update();
                }
            });
        }
    </script>

@endPushIf