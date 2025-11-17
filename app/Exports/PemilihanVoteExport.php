<?php

namespace App\Exports;

use App\Models\Pemilihan;
use App\Models\PemilihanVote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PemilihanVoteExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * Cache jenis options so we don't re-read the array in each map call.
     *
     * @var array<string, string>
     */
    protected array $jenisOptions;

    public function __construct()
    {
        $this->jenisOptions = Pemilihan::jenisOptions();
    }

    public function collection(): Collection
    {
        return PemilihanVote::with(['pemilihan', 'candidate', 'user.prodis'])
            ->orderByDesc('voted_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Vote',
            'Pemilihan',
            'Jenis Pemilihan',
            'Nomor Urut',
            'Nama Calon',
            'Ketua',
            'Wakil',
            'Nama Pemilih',
            'NIM',
            'Email',
            'Program Studi',
            'Waktu Memilih',
        ];
    }

    /**
     * @param  \App\Models\PemilihanVote  $vote
     */
    public function map($vote): array
    {
        $pemilihan = $vote->pemilihan;
        $candidate = $vote->candidate;
        $user = $vote->user;

        $jenis = $pemilihan ? ($this->jenisOptions[$pemilihan->jenis] ?? $pemilihan->jenis) : '-';
        $votedAt = $vote->voted_at
            ? $vote->voted_at->timezone(config('app.timezone'))->format('d-m-Y H:i:s')
            : '-';

        return [
            $vote->id,
            optional($pemilihan)->name ?? '-',
            $jenis,
            optional($candidate)->nomor_urut ?? '-',
            optional($candidate)->name ?? '-',
            optional($candidate)->ketua_nama ?? '-',
            optional($candidate)->wakil_nama ?? '-',
            optional($user)->name ?? '-',
            optional($user)->nim ?? '-',
            optional($user)->email ?? '-',
            optional(optional($user)->prodis)->name ?? '-',
            $votedAt,
        ];
    }
}
