<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\Tahun;
use App\Models\TahunAkademik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineGate();
        Carbon::setLocale('id');
        $this->cekTahun();
        $this->cekTahunAkademik();
    }

    private function cekTahun()
    {
        $tahun = date('Y');
        $cek = Tahun::where('tahun', $tahun)->first();
        if (!$cek) {
            Tahun::create(['tahun' => $tahun]);
        }
    }

    private function cekTahunAkademik() {
        $tahunAkademik = (string)date('Y') . '/' . (string)(date('Y') + 1);
        $cek = TahunAkademik::where('tahun_akademik', $tahunAkademik)->first();
        if (!$cek) {
            TahunAkademik::create(['tahun_akademik' => $tahunAkademik]);
        }
    }

    private function defineGate()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            Gate::define($role->gate_name, function (User $user) use ($role) {
                return $user->role == $role->id;
            });
        }
    }
}
