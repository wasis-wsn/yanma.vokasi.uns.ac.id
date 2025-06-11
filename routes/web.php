<?php

use App\Http\Controllers\AkreditasiController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\UserController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiluarJadwalController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KeringananUKTController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\LegalisirController;
use App\Http\Controllers\LembarPengesahanTAController;
use App\Http\Controllers\LPJController;
use App\Http\Controllers\PembinaController;
use App\Http\Controllers\PenundaanController;
use App\Http\Controllers\PerpanjanganStudiController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\SelangController;
use App\Http\Controllers\SIKController;
use App\Http\Controllers\SKLController;
use App\Http\Controllers\SKMKController;
use App\Http\Controllers\SKPIController;
use App\Http\Controllers\SuketController;
use App\Http\Controllers\SuratHasilController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TranskripNilaiController;
use App\Http\Controllers\UndurDiriController;
use App\Http\Controllers\VerifikasiWisudaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $berita = \App\Models\Berita::latest()->paginate(6);
    return view('landingpage.index', compact('berita'));
})->name('home');

/* * * * * * * * * * * * * * * * *
*                                *
*   Login Email & Password       *
*                                *
* * * * * * * * * * * * * * * * */
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'prosesLogin'])->middleware('throttle:5,1')->name('prosesLogin');

/* * * * * * * * * * * * * * * * *
*                                *
*   Login Email uns.ac.id        *
*                                *
* * * * * * * * * * * * * * * * */
Route::get('/auth/google', [AuthController::class, 'redirectGoogle'])->name('google.login');
Route::get('/registrasi', [AuthController::class, 'create'])->name('google.registrasi');
Route::post('/registrasi', [AuthController::class, 'register'])->name('register');
Route::get('/auth/google/callback', [AuthController::class, 'callback']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/* * * * * * * * * * * * * * * * *
*                                *
*   LandingPage Keringanan UKT   *
*                                *
* * * * * * * * * * * * * * * * */
Route::get('/ukt/informasi', [KeringananUKTController::class, 'landingPage'])->name('ukt.landingPage');

/* * * * * * * * * * * * * * *
*                            *
*   LandingPage Legalisir    *
*                            *
* * * * * * * * * * * * * * */
Route::name('legalisir.')->prefix('legalisir')->group(function () {
    Route::get('/informasi', [LegalisirController::class, 'landingPage'])->name('landingPage');
    Route::get('/search', [LegalisirController::class, 'search'])->name('search');
    Route::get('/detail', [LegalisirController::class, 'detail'])->name('detail');
});

/* * * * * * * * * * * * * * *
*                            *
*   LandingPage Akreditasi   *
*                            *
* * * * * * * * * * * * * * */
Route::get('/akreditasi/lp', [AkreditasiController::class, 'landingPage'])->name('akreditasi.landingPage');
Route::get('/akreditasi/lp/getData', [AkreditasiController::class, 'getAkreditasi'])->name('akreditasi.landingPage.getData');

/* * * * * * * * * * * * * * * * *
*                                *
*   LandingPage Kontak           *
*                                *
* * * * * * * * * * * * * * * * */
Route::get('/contact/lp', [ContactController::class, 'landingPage'])->name('contact.landingPage');

Route::middleware('auth')->group(function () {

    Route::get('/login/rahasia/ufytudgjgygkuhkhjhg/{email}', [AuthController::class, 'rahasia'])->name('rahasia')->middleware('role:staff');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');
    Route::get('/get_mhs', [DashboardController::class, 'get_mhs'])->name('get_mhs');
    Route::get('/get_prodi', [DashboardController::class, 'get_prodi'])->name('get_prodi');
    Route::get('/get_dosen', [DashboardController::class, 'get_dosen'])->name('get_dosen');

    Route::name('grafik.')->prefix('grafik')->group(function () {
        Route::get('/diluarjadwal/{tahun}', [GrafikController::class, 'diluarjadwal'])->name('diluarjadwal');
        Route::get('/cuti/{tahun}', [GrafikController::class, 'cuti'])->name('cuti');
        Route::get('/surattugas/{tahun}', [GrafikController::class, 'surattugas'])->name('surattugas');
    });

    /* * * * * * * * * * * * *
    *                        *
    *   Menu Profile         *
    *                        *
    * * * * * * * * * * * * */
    Route::name('user.')->prefix('user')->group(function () {
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');

        Route::middleware(['auth', 'role:staff'])->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/list', [UserController::class, 'list'])->name('list');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::delete('/{id}/delete', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    /* * * * * * * * * * * * *
    *                        *
    *   Menu Setting         *
    *                        *
    * * * * * * * * * * * * */
    Route::middleware('role:staff')->group(function () {
        Route::name('akreditasi.')->prefix('akreditasi')->group(function () {
            Route::get('/', [AkreditasiController::class, 'index'])->name('index');
            Route::get('/list', [AkreditasiController::class, 'list'])->name('list');
            Route::post('/', [AkreditasiController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AkreditasiController::class, 'edit'])->name('edit');
            Route::post('/{id}', [AkreditasiController::class, 'update'])->name('update');
            Route::delete('/{id}', [AkreditasiController::class, 'destroy'])->name('destroy');
        });
        Route::name('contact.')->prefix('contact')->group(function () {
            Route::get('/', [ContactController::class, 'index'])->name('index');
            Route::get('/list', [ContactController::class, 'list'])->name('list');
            Route::post('/', [ContactController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ContactController::class, 'edit'])->name('edit');
            Route::post('/{id}', [ContactController::class, 'update'])->name('update');
            Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
        });
        Route::name('berita.')->prefix('berita')->group(function () {
            Route::get('/', [BeritaController::class, 'index'])->name('index');
            Route::get('/list', [BeritaController::class, 'list'])->name('list');
            Route::get('/create', [BeritaController::class, 'create'])->name('create');
            Route::post('/', [BeritaController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [BeritaController::class, 'edit'])->name('edit');
            Route::post('/{id}', [BeritaController::class, 'update'])->name('update');
            Route::delete('/{id}', [BeritaController::class, 'destroy'])->name('destroy');
        });
        Route::name('prodi.')->prefix('prodi')->group(function () {
            Route::get('/', [ProdiController::class, 'index'])->name('index');
            Route::get('/list', [ProdiController::class, 'list'])->name('list');
            Route::post('/', [ProdiController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ProdiController::class, 'edit'])->name('edit');
            Route::post('/{id}', [ProdiController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProdiController::class, 'destroy'])->name('destroy');
        });

        Route::name('pembina.')->prefix('pembina')->group(function () {
            Route::get('/', [PembinaController::class, 'index'])->name('index');
            Route::get('/list', [PembinaController::class, 'list'])->name('list');
            Route::post('/', [PembinaController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PembinaController::class, 'edit'])->name('edit');
            Route::post('/{id}', [PembinaController::class, 'update'])->name('update');
            Route::delete('/{id}', [PembinaController::class, 'destroy'])->name('destroy');
        });

        Route::name('template.')->prefix('tpl')->group(function () {
            Route::get('/', [TemplateController::class, 'index'])->name('index');
            Route::get('/list', [TemplateController::class, 'list'])->name('list');
            Route::post('/', [TemplateController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TemplateController::class, 'edit'])->name('edit');
            Route::post('/{id}', [TemplateController::class, 'update'])->name('update');
            Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('destroy');
        });

        Route::name('layanan.')->prefix('layanan')->group(function () {
            Route::get('/', [LayananController::class, 'index'])->name('index');
            Route::get('/list', [LayananController::class, 'list'])->name('list');
            Route::post('/', [LayananController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [LayananController::class, 'edit'])->name('edit');
            Route::post('/{id}', [LayananController::class, 'update'])->name('update');
            Route::delete('/{id}', [LayananController::class, 'destroy'])->name('destroy');
        });

        Route::name('suratHasil.')->prefix('suratHasil')->group(function () {
            Route::get('/', [SuratHasilController::class, 'index'])->name('index');
            Route::get('/list', [SuratHasilController::class, 'list'])->name('list');
            Route::post('/{id}', [SuratHasilController::class, 'update'])->name('update');
        });
    });

    /* * * * * * * * * * * * *
    *                        *
    *   Menu Akademik        *
    *                        *
    * * * * * * * * * * * * */
    Route::name('perpanjanganStudi.')->prefix('perpanjanganStudi')->group(function () {
        Route::get('/', [PerpanjanganStudiController::class, 'index'])->name('index')->middleware(['role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi']);
        Route::get('/listMahasiswa', [PerpanjanganStudiController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [PerpanjanganStudiController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [PerpanjanganStudiController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [PerpanjanganStudiController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [PerpanjanganStudiController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/setting/update', [PerpanjanganStudiController::class, 'setting'])->name('setting')->middleware('role:staff');
        Route::post('/store', [PerpanjanganStudiController::class, 'store'])->name('store')->middleware('role:mahasiswa,staff');
        Route::post('/show/{id}', [PerpanjanganStudiController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::post('/revisi/{id}', [PerpanjanganStudiController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [PerpanjanganStudiController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [PerpanjanganStudiController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/proses/{id}', [PerpanjanganStudiController::class, 'proses'])->name('proses')->middleware('role:staff,fo');
        Route::post('/bulk-process', [PerpanjanganStudiController::class, 'bulkProcess'])->name('bulkProcess')->middleware('role:staff');
    });

    Route::name('penundaan.')->prefix('penundaan')->group(function () {
        Route::get('/', [PenundaanController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::get('/listMahasiswa', [PenundaanController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [PenundaanController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listDekanat', [PenundaanController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [PenundaanController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/setting/update', [PenundaanController::class, 'setting'])->name('setting')->middleware('role:staff');
        Route::post('/export/data', [PenundaanController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/store', [PenundaanController::class, 'store'])->name('store')->middleware('role:mahasiswa,staff');
        Route::post('/show/{id}', [PenundaanController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::post('/revisi/{id}', [PenundaanController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [PenundaanController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/proses/{id}', [PenundaanController::class, 'proses'])->name('proses')->middleware('role:staff');
        Route::post('/bulk-process', [PenundaanController::class, 'bulkProcess'])->name('bulkProcess')->middleware('role:staff');
    });

    Route::name('selang.')->prefix('selang')->group(function () {
        Route::get('/', [SelangController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::get('/listMahasiswa', [SelangController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [SelangController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [SelangController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [SelangController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [SelangController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/setting/update', [SelangController::class, 'setting'])->name('setting')->middleware('role:staff');
        Route::post('/store', [SelangController::class, 'store'])->name('store')->middleware('role:mahasiswa,staff');
        Route::post('/show/{id}', [SelangController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::post('/revisi/{id}', [SelangController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [SelangController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [SelangController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor');
        Route::post('/proses/{id}', [SelangController::class, 'proses'])->name('proses')->middleware('role:staff,fo');
        Route::post('/bulk-process', [SelangController::class, 'bulkProcess'])->name('bulkProcess')->middleware('role:staff');
    });

    Route::name('undurDiri.')->prefix('undurDiri')->group(function () {
        Route::get('/', [UndurDiriController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::get('/listMahasiswa', [UndurDiriController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [UndurDiriController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [UndurDiriController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [UndurDiriController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [UndurDiriController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/setting/update', [UndurDiriController::class, 'setting'])->name('setting')->middleware('role:staff');
        Route::post('/store', [UndurDiriController::class, 'store'])->name('store')->middleware('role:mahasiswa,staff');
        Route::post('/show/{id}', [UndurDiriController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::post('/revisi/{id}', [UndurDiriController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [UndurDiriController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [UndurDiriController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/proses/{id}', [UndurDiriController::class, 'proses'])->name('proses')->middleware('role:staff,fo');
    });

    Route::name('diluarJadwal.')->prefix('diluarJadwal')->group(function () {
        Route::get('/', [DiluarJadwalController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::get('/listMahasiswa', [DiluarJadwalController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [DiluarJadwalController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [DiluarJadwalController::class, 'listFo'])->name('listFo')->middleware('role:fo,adminprodi');
        Route::get('/listDekanat', [DiluarJadwalController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [DiluarJadwalController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/setting/update', [DiluarJadwalController::class, 'setting'])->name('setting')->middleware('role:staff');
        Route::post('/store', [DiluarJadwalController::class, 'store'])->name('store')->middleware('role:mahasiswa,staff');
        Route::post('/show/{id}', [DiluarJadwalController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::post('/revisi/{id}', [DiluarJadwalController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [DiluarJadwalController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [DiluarJadwalController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/proses/{id}', [DiluarJadwalController::class, 'proses'])->name('proses')->middleware('role:staff,fo');
    });

    Route::name('ukt.')->prefix('ukt')->middleware('role:staff')->group(function () {
        Route::get('/', [KeringananUKTController::class, 'index'])->name('index');
        Route::get('/show', [KeringananUKTController::class, 'show'])->name('show');
        Route::post('/update/{id}', [KeringananUKTController::class, 'update'])->name('update');
    });

    Route::name('TA.')->prefix('TA')->group(function () {
        Route::get('/', [LembarPengesahanTAController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [LembarPengesahanTAController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor,adminprodi');
        Route::post('/TA', [LembarPengesahanTAController::class, 'store'])->name('store')->middleware('role:mahasiswa,fo');
        Route::get('/show/{id}', [LembarPengesahanTAController::class, 'showTA'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::post('/proses/{id}', [LembarPengesahanTAController::class, 'proses'])->name('proses')->middleware('role:fo');
        Route::delete('/delete/{id}', [LembarPengesahanTAController::class, 'destroy'])->name('destroy')->middleware('role:fo');
    });

    Route::name('skl.')->prefix('skl')->group(function () {
        Route::get('/', [SKLController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::get('/listStaff', [SKLController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [SKLController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [SKLController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor,adminprodi');
        Route::post('/export/data', [SKLController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/store', [SKLController::class, 'store'])->name('store')->middleware('role:mahasiswa');
        Route::post('/show/{id}', [SKLController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo,adminprodi');
        Route::post('/revisi/{id}', [SKLController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::post('/proses/{id}', [SKLController::class, 'proses'])->name('proses')->middleware('role:staff,fo');
    });

    Route::name('verifikasiWisuda.')->prefix('verifikasiWisuda')->group(function () {
        Route::get('/', [VerifikasiWisudaController::class, 'index'])->name('index')->middleware('role:staff,dekanat,subkoor');
        Route::get('/list', [VerifikasiWisudaController::class, 'list'])->name('list')->middleware('role:staff');
        Route::get('/listDekanat', [VerifikasiWisudaController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::post('/', [VerifikasiWisudaController::class, 'store'])->name('store')->middleware('role:mahasiswa');
        Route::post('/export/data', [VerifikasiWisudaController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor');
        Route::get('/show/{id}', [VerifikasiWisudaController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor');
        Route::post('/update/{id}', [VerifikasiWisudaController::class, 'update'])->name('update')->middleware('role:mahasiswa');
        Route::post('/proses/{id}', [VerifikasiWisudaController::class, 'proses'])->name('proses')->middleware('role:staff');
    });

    Route::name('transkrip.')->prefix('transkrip')->group(function () {
        Route::get('/', [TranskripNilaiController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo');
        Route::get('/listStaff', [TranskripNilaiController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [TranskripNilaiController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [TranskripNilaiController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/{id}', [TranskripNilaiController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo');
        Route::post('/export', [TranskripNilaiController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor');
        Route::post('/{id}', [TranskripNilaiController::class, 'update'])->name('update')->middleware('role:staff,fo');
        Route::post('/update/many', [TranskripNilaiController::class, 'updateMany'])->name('updateMany')->middleware('role:staff,fo');
    });

    Route::name('skpi.')->prefix('skpi')->group(function () {
        Route::get('/', [SKPIController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo');
        Route::get('/listStaff', [SKPIController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listFo', [SKPIController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listDekanat', [SKPIController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/{id}', [SKPIController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,fo');
        Route::post('/export', [SKPIController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor');
        Route::post('/{id}', [SKPIController::class, 'update'])->name('update')->middleware('role:staff,fo');
        Route::post('/update/many', [SKPIController::class, 'updateMany'])->name('updateMany')->middleware('role:staff,fo');
    });

    /* * * * * * * * * * * * *
    *                        *
    *   Menu Kemahasiswaan   *
    *                        *
    * * * * * * * * * * * * */
    Route::name('suket.')->prefix('suket')->group(function () {
        Route::get('/', [SuketController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::get('/listMahasiswa', [SuketController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [SuketController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listDekanat', [SuketController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [SuketController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/', [SuketController::class, 'store'])->name('store')->middleware('role:mahasiswa');
        Route::post('/show/{id}', [SuketController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::post('/revisi/{id}', [SuketController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [SuketController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [SuketController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/{id}', [SuketController::class, 'proses'])->name('proses')->middleware('role:staff');
        Route::get('/generate/suratHasil/{id}', [SuketController::class, 'generateSurat'])->name('generate')->middleware('role:staff');
    });

    Route::name('skmk.')->prefix('skmk')->group(function () {
        Route::get('/', [SKMKController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::get('/listMahasiswa', [SKMKController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [SKMKController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listDekanat', [SKMKController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [SKMKController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/', [SKMKController::class, 'store'])->name('store')->middleware('role:mahasiswa');
        Route::post('/show/{id}', [SKMKController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::post('/revisi/{id}', [SKMKController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [SKMKController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [SKMKController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/{id}', [SKMKController::class, 'proses'])->name('proses')->middleware('role:staff');
        Route::get('/generate/suratHasil/{id}', [SKMKController::class, 'generateSurat'])->name('generate')->middleware('role:staff');
    });

    Route::name('st.')->prefix('st')->group(function () {
        Route::get('/', [SuratTugasController::class, 'index'])->name('index')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::get('/listMahasiswa', [SuratTugasController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listStaff', [SuratTugasController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listDekanat', [SuratTugasController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [SuratTugasController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/', [SuratTugasController::class, 'store'])->name('store')->middleware('role:mahasiswa');
        Route::post('/show/{id}', [SuratTugasController::class, 'show'])->name('show')->middleware('role:mahasiswa,staff,dekanat,subkoor,adminprodi');
        Route::post('/revisi/{id}', [SuratTugasController::class, 'revisi'])->name('revisi')->middleware('role:mahasiswa');
        Route::delete('/{id}', [SuratTugasController::class, 'destroy'])->name('destroy')->middleware('role:mahasiswa');
        Route::post('/export/data', [SuratTugasController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor');
        Route::post('/{id}', [SuratTugasController::class, 'proses'])->name('proses')->middleware('role:staff');
        Route::get('/generate/suratHasil/{id}', [SuratTugasController::class, 'generateSurat'])->name('generate')->middleware('role:staff');
    });

    Route::name('sik.')->prefix('sik')->group(function () {
        Route::get('/', [SIKController::class, 'index'])->name('index')->middleware('role:ormawa,staff,dekanat,subkoor,adminprodi');
        Route::get('/listOrmawa', [SIKController::class, 'listOrmawa'])->name('listOrmawa')->middleware('role:ormawa');
        Route::get('/listStaff', [SIKController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listDekanat', [SIKController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [SIKController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/', [SIKController::class, 'store'])->name('store')->middleware('role:ormawa');
        Route::post('/show/{id}', [SIKController::class, 'show'])->name('show')->middleware('role:ormawa,staff,dekanat,subkoor,adminprodi');
        Route::post('/revisi/{id}', [SIKController::class, 'revisi'])->name('revisi')->middleware('role:ormawa');
        Route::delete('/{id}', [SIKController::class, 'destroy'])->name('destroy')->middleware('role:ormawa');
        Route::post('/export/data', [SIKController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor,adminprodi');
        Route::post('/{id}', [SIKController::class, 'proses'])->name('proses')->middleware('role:staff');
        Route::get('/generate/suratHasil/{id}', [SIKController::class, 'generateSurat'])->name('generate')->middleware('role:staff');
    });

    Route::name('lpj.')->prefix('lpj')->group(function () {
        Route::get('/', [LPJController::class, 'index'])->name('index')->middleware('role:mahasiswa,ormawa,staff,dekanat,subkoor,adminprodi');
        Route::get('/listMahasiswa', [LPJController::class, 'listMahasiswa'])->name('listMahasiswa')->middleware('role:mahasiswa');
        Route::get('/listOrmawa', [LPJController::class, 'listOrmawa'])->name('listOrmawa')->middleware('role:ormawa');
        Route::get('/listStaff', [LPJController::class, 'listStaff'])->name('listStaff')->middleware('role:staff');
        Route::get('/listDekanat', [LPJController::class, 'listDekanat'])->name('listDekanat')->middleware('role:dekanat,subkoor');
        Route::get('/listAdminProdi', [LPJController::class, 'listAdminProdi'])->name('listAdminProdi')->middleware('role:adminprodi');
        Route::post('/upload/{id}', [LPJController::class, 'upload'])->name('upload')->middleware('role:ormawa,mahasiswa');
        Route::post('/proses/{id}', [LPJController::class, 'proses'])->name('proses')->middleware('role:staff');
    });

    /* * * * * * * * * * * * *
    *                        *
    *   Menu Alumni          *
    *                        *
    * * * * * * * * * * * * */
    Route::name('legalisir.')->prefix('legalisir')->group(function () {
        Route::get('/', [LegalisirController::class, 'index'])->name('index')->middleware('role:fo,staff,dekanat,subkoor');
        Route::get('/listFo', [LegalisirController::class, 'listFo'])->name('listFo')->middleware('role:fo');
        Route::get('/listStaff', [LegalisirController::class, 'listStaff'])->name('listStaff')->middleware('role:staff,dekanat,subkoor');
        Route::post('/export', [LegalisirController::class, 'export'])->name('export')->middleware('role:staff,dekanat,subkoor');
        Route::post('/', [LegalisirController::class, 'store'])->name('store')->middleware('role:fo');
        Route::post('/show/{id}', [LegalisirController::class, 'show'])->name('show')->middleware('role:fo,staff,dekanat,subkoor');
        Route::post('/update/{id}', [LegalisirController::class, 'update'])->name('update')->middleware('role:fo');
        Route::delete('/destroy/{id}', [LegalisirController::class, 'destroy'])->name('destroy')->middleware('role:fo');
        Route::post('/proses/{id}', [LegalisirController::class, 'proses'])->name('proses')->middleware('role:fo');
    });
});
