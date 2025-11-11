(function ($) {
    const Pemilihan = {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            $(document).on('click', '.btn-vote-pemilihan', this.handleVote.bind(this));
        },

        handleVote(event) {
            event.preventDefault();
            const button = $(event.currentTarget);
            const card = button.closest('.pemilihan-card');
            const voteUrl = card.data('vote-url');
            const candidateId = button.data('candidate');

            if (!voteUrl || !candidateId) {
                return;
            }

            Swal.fire({
                title: 'Kirim Suara?',
                text: 'Suara yang sudah dikirim tidak dapat diubah.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitVote(card, voteUrl, candidateId);
                }
            });
        },

        submitVote(card, url, candidateId) {
            $.ajax({
                url: url,
                type: 'POST',
                data: { candidate_id: candidateId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                beforeSend: () => {
                    card.addClass('opacity-50');
                },
                success: (response) => {
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.message || 'Suara Anda berhasil dikirim.',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false,
                    });

                    if (response.data) {
                        this.refreshCard(card, response.data);
                    } else {
                        window.location.reload();
                    }
                },
                error: (xhr) => {
                    let message = 'Terjadi kesalahan, silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal', message, 'error');
                },
                complete: () => {
                    card.removeClass('opacity-50');
                },
            });
        },

        refreshCard(card, payload) {
            if (!payload || !payload.candidates) {
                return;
            }

            const tbody = card.find('tbody');
            const isOpen = payload.pemilihan ? payload.pemilihan.is_open : false;
            const userVoteId = payload.user_vote ? payload.user_vote.candidate_id : null;

            const badge = card.find('.pemilihan-status-badge');
            if (badge.length) {
                badge.removeClass('bg-success bg-secondary');
                badge.addClass(isOpen ? 'bg-success' : 'bg-secondary');
                badge.text(isOpen ? 'Sedang Dibuka' : 'Belum Dibuka');
            }

            tbody.empty();

            if (!payload.candidates.length) {
                tbody.append('<tr><td colspan="4" class="text-center text-muted">Belum ada calon yang terdaftar.</td></tr>');
                return;
            }

            payload.candidates.forEach((candidate) => {
                const row = $('<tr>');
                row.append(`<td><span class="fw-semibold">${candidate.nomor_urut}</span></td>`);

                let detailHtml = `<div class="fw-semibold text-dark">${candidate.name}</div>`;
                if (candidate.visi) {
                    detailHtml += `<div class="small text-muted mb-1">Visi: ${candidate.visi}</div>`;
                }
                if (candidate.misi) {
                    detailHtml += `<div class="small text-muted mb-1">Misi: ${candidate.misi}</div>`;
                }
                if (candidate.deskripsi) {
                    detailHtml += `<div class="small text-muted">${candidate.deskripsi}</div>`;
                }
                row.append(`<td>${detailHtml}</td>`);
                const totalVotes = typeof candidate.total_votes === 'number' ? candidate.total_votes : 0;
                row.append(`<td class="text-center"><span class="fw-semibold">${totalVotes}</span></td>`);

                const actionTd = $('<td class="text-end"></td>');
                if (!isOpen) {
                    actionTd.append('<span class="badge bg-secondary">Ditutup</span>');
                } else if (userVoteId && userVoteId === candidate.id) {
                    actionTd.append('<span class="badge bg-success">Pilihanmu</span>');
                } else if (userVoteId) {
                    actionTd.append('<button class="btn btn-sm btn-outline-secondary" disabled>Vote</button>');
                } else {
                    actionTd.append(`<button class="btn btn-sm btn-primary btn-vote-pemilihan" data-candidate="${candidate.id}">Pilih</button>`);
                }
                row.append(actionTd);

                tbody.append(row);
            });
        },
    };

    $(document).ready(() => {
        Pemilihan.init();
    });
})(jQuery);
