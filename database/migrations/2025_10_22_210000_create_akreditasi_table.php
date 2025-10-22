<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('akreditasis') && !Schema::hasTable('akreditasi')) {
            Schema::rename('akreditasis', 'akreditasi');
        }

        if (!Schema::hasTable('akreditasi')) {
            Schema::create('akreditasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prodi_id')
                    ->constrained('ref_prodi')
                    ->cascadeOnDelete();
                $table->string('file');
                $table->date('tanggal_awal')->nullable();
                $table->date('tanggal_akhir')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('akreditasi', function (Blueprint $table) {
                if (!Schema::hasColumn('akreditasi', 'file')) {
                    $table->string('file')->after('prodi_id');
                }

                if (!Schema::hasColumn('akreditasi', 'tanggal_awal')) {
                    $table->date('tanggal_awal')->nullable()->after('file');
                }

                if (!Schema::hasColumn('akreditasi', 'tanggal_akhir')) {
                    $table->date('tanggal_akhir')->nullable()->after('tanggal_awal');
                }

                if (!Schema::hasColumn('akreditasi', 'deleted_at')) {
                    $table->softDeletes();
                }
            });

            if (Schema::hasColumn('akreditasi', 'tahun')) {
                DB::table('akreditasi')
                    ->select('id', 'tahun')
                    ->whereNotNull('tahun')
                    ->orderBy('id')
                    ->chunk(100, function ($rows) {
                        foreach ($rows as $row) {
                            $raw = trim($row->tahun ?? '');
                            if ($raw === '') {
                                continue;
                            }

                            $start = null;
                            $end = null;

                            if (preg_match('/^(\d{4})$/', $raw, $matches)) {
                                $start = Carbon::createFromDate((int) $matches[1], 1, 1);
                                $end = $start->copy()->endOfYear();
                            } elseif (preg_match('/^(\d{4})\D+(\d{4})$/', $raw, $matches)) {
                                $start = Carbon::createFromDate((int) $matches[1], 1, 1);
                                $end = Carbon::createFromDate((int) $matches[2], 12, 31);
                            }

                            if ($start && $end) {
                                DB::table('akreditasi')
                                    ->where('id', $row->id)
                                    ->update([
                                        'tanggal_awal' => $start->toDateString(),
                                        'tanggal_akhir' => $end->toDateString(),
                                    ]);
                            }
                        }
                    });
            }

            if (Schema::hasColumn('akreditasi', 'tahun')) {
                Schema::table('akreditasi', function (Blueprint $table) {
                    $table->dropColumn('tahun');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akreditasi');
    }
};
