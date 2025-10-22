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
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('tahuns')) {
                return;
            }

            $tahun = date('Y');
            $cek = Tahun::where('tahun', $tahun)->first();
            if (!$cek) {
                Tahun::create(['tahun' => $tahun]);
            }
        } catch (\Exception $e) {
            // Skip jika ada error
        }
    }

    private function cekTahunAkademik() {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('tahun_akademik')) {
                return;
            }

            $tahunAkademik = (string)date('Y') . '/' . (string)(date('Y') + 1);
            $cek = TahunAkademik::where('tahun_akademik', $tahunAkademik)->first();
            if (!$cek) {
                TahunAkademik::create(['tahun_akademik' => $tahunAkademik]);
            }
        } catch (\Exception $e) {
            // Skip jika ada error
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
