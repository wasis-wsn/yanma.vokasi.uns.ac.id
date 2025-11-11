@php
    $cardClasses = $pemilihan ? 'pemilihan-card h-100' : 'pemilihan-card h-100 disabled';
    $hasPemilihan = !is_null($pemilihan);
    $voteUrl = $hasPemilihan ? route('pemilihan.vote', $pemilihan) : null;
    $summaryUrl = $hasPemilihan ? route('pemilihan.summary', $pemilihan) : null;
    $userVote = $hasPemilihan ? optional($pemilihan->votes->first()) : null;
    $userVoteId = $userVote?->candidate_id;
@endphp
<div class="card {{ $cardClasses }}"
     data-jenis="{{ $jenis }}"
     @if($hasPemilihan)
        data-vote-url="{{ $voteUrl }}"
        data-summary-url="{{ $summaryUrl }}"
     @endif
>
    <div class="card-header d-flex justify-content-between align-items-start">
        <div>
            <h4 class="card-title mb-1">{{ $title }}</h4>
            <p class="text-muted mb-0">
                @if($hasPemilihan && $pemilihan->deskripsi)
                    {{ $pemilihan->deskripsi }}
                @else
                    Pantau jadwal resmi untuk mengikuti pemilihan.
                @endif
            </p>
        </div>
        <span class="badge pemilihan-status-badge {{ $hasPemilihan && $pemilihan->votingWindowIsOpen() ? 'bg-success' : 'bg-secondary' }}">
            {{ $hasPemilihan ? ($pemilihan->votingWindowIsOpen() ? 'Sedang Dibuka' : 'Belum Dibuka') : 'Belum Tersedia' }}
        </span>
    </div>
    <div class="card-body">
        @if(!$hasPemilihan)
            <p class="text-muted mb-0">Belum ada pemilihan aktif untuk kategori ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0 pemilihan-table">
                    <thead class="small text-muted">
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Calon</th>
                            <th class="text-center" style="width: 90px;">Perolehan</th>
                            <th class="text-end" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pemilihan->candidates as $candidate)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $candidate->nomor_urut }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $candidate->name }}</div>
                                    @if($candidate->visi)
                                        <div class="small text-muted mb-1">Visi: {{ $candidate->visi }}</div>
                                    @endif
                                    @if($candidate->misi)
                                        <div class="small text-muted mb-1">Misi: {{ $candidate->misi }}</div>
                                    @endif
                                    @if($candidate->deskripsi)
                                        <div class="small text-muted">{{ $candidate->deskripsi }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $candidate->total_votes ?? 0 }}</span>
                                </td>
                                <td class="text-end">
                                    @if(!$pemilihan->votingWindowIsOpen())
                                        <span class="badge bg-secondary">Ditutup</span>
                                    @elseif($userVoteId === $candidate->id)
                                        <span class="badge bg-success">Pilihanmu</span>
                                    @elseif($userVoteId)
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Vote</button>
                                    @else
                                        <button class="btn btn-sm btn-primary btn-vote-pemilihan"
                                            data-candidate="{{ $candidate->id }}">
                                            Pilih
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada calon yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="small text-muted mt-3 mb-0">
                Kamu hanya dapat memilih satu kali. Pastikan pilihanmu sudah tepat sebelum mengirimkan suara.
            </p>
        @endif
    </div>
</div>
