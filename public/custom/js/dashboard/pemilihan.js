(function ($) {
    const PemilihanStepper = {
        init() {
            this.root = $('#pemilihanStepper');
            if (!this.root.length) {
                return;
            }

            this.config = this.parseConfig();
            this.state = {
                step: 1,
                totalSteps: 3,
                submitting: false,
                choices: {
                    presbem: this.buildChoiceState('presbem'),
                    caleg: this.buildChoiceState('caleg'),
                },
            };

            this.optionMap = {
                presbem: {},
                caleg: {},
            };

            this.cacheDom();
            this.mapOptions();
            this.applyInitialSelections();
            this.bindEvents();
            this.refreshSummary();
            this.updateStepUI();
        },

        parseConfig() {
            let config = this.root.data('config');
            if (typeof config === 'string') {
                try {
                    config = JSON.parse(config);
                } catch (error) {
                    config = {};
                }
            }
            return config || {};
        },

        buildChoiceState(type) {
            const data = this.config[type] || {};
            const hasVote = data && data.user_vote !== undefined && data.user_vote !== null && data.user_vote !== '';
            const enabled = !!data.enabled;
            const isOpen = !!data.is_open;
            const isEligible = type === 'caleg' ? data.eligible !== false : true;

            return {
                enabled,
                isOpen,
                eligible: isEligible,
                voteUrl: data.vote_url || null,
                selected: hasVote ? data.user_vote : null,
                initial: hasVote ? data.user_vote : null,
                locked: !isEligible || (enabled ? (!isOpen || hasVote) : true),
            };
        },

        cacheDom() {
            this.prevBtn = this.root.find('.stepper-prev');
            this.nextBtn = this.root.find('.stepper-next');
            this.stepperItems = this.root.find('.stepper-item');
            this.stepPanes = this.root.find('[data-step-pane]');
            this.summaryNote = this.root.find('[data-summary-note]');
        },

        mapOptions() {
            const self = this;
            this.root.find('.pemilihan-option').each(function () {
                const option = $(this);
                const type = option.data('pemilihan');
                const id = option.data('candidateId');
                if (!type || !id) {
                    return;
                }
                if (!self.optionMap[type]) {
                    self.optionMap[type] = {};
                }
                self.optionMap[type][String(id)] = {
                    name: option.data('candidateName'),
                    nomor: option.data('candidateNomor'),
                    description: option.data('candidateDescription') || '',
                };
            });
        },

        bindEvents() {
            this.root.on('click', '.pemilihan-option', this.handleOptionClick.bind(this));
            this.prevBtn.on('click', this.handlePrev.bind(this));
            this.nextBtn.on('click', this.handleNext.bind(this));
            this.root.on('click', '.stepper-edit', this.handleEditRequest.bind(this));
        },

        applyInitialSelections() {
            Object.keys(this.state.choices).forEach((type) => {
                const choice = this.state.choices[type];
                if (choice.selected) {
                    this.highlightSelection(type, choice.selected);
                }
            });
        },

        handleOptionClick(event) {
            const option = $(event.currentTarget);
            const type = option.data('pemilihan');
            if (!type || option.hasClass('is-disabled')) {
                return;
            }
            if (!this.canModify(type)) {
                return;
            }

            const candidateId = option.data('candidateId');
            // Allow candidateId = 0 (kotak kosong), but not undefined/null
            if (candidateId === undefined || candidateId === null) {
                return;
            }

            this.state.choices[type].selected = candidateId;

            this.highlightSelection(type, candidateId);
            this.refreshSummary();
            this.updateStepUI();
        },

        canModify(type) {
            const choice = this.state.choices[type];
            if (!choice || !choice.enabled) {
                return false;
            }
             if (!choice.eligible) {
                 return false;
             }
            if (choice.locked) {
                return false;
            }
            if (!choice.isOpen) {
                return false;
            }
            return true;
        },

        highlightSelection(type, candidateId) {
            const options = this.root.find(`.pemilihan-option[data-pemilihan="${type}"]`);
            options.removeClass('is-selected');
            // Allow candidateId = 0 (kotak kosong)
            if (candidateId === undefined || candidateId === null) {
                return;
            }

            options.each(function () {
                const option = $(this);
                if (String(option.data('candidateId')) === String(candidateId)) {
                    option.addClass('is-selected');
                }
            });
        },

        handlePrev() {
            if (this.state.step === 1) {
                return;
            }
            this.state.step = Math.max(1, this.state.step - 1);
            this.updateStepUI();
        },

        handleNext() {
            if (this.state.step < this.state.totalSteps) {
                if (this.state.step === 1 && !this.isStepSatisfied('presbem')) {
                    return;
                }
                if (this.state.step === 2 && !this.isStepSatisfied('caleg')) {
                    return;
                }
                this.state.step = Math.min(this.state.totalSteps, this.state.step + 1);
                this.updateStepUI();
                return;
            }

            this.handleSubmit();
        },

        handleEditRequest(event) {
            const button = $(event.currentTarget);
            if (button.is(':disabled')) {
                return;
            }
            const target = Number(button.data('targetStep'));
            if (!target) {
                return;
            }
            this.state.step = target;
            this.updateStepUI();
        },

        isStepSatisfied(type) {
            const choice = this.state.choices[type];
            if (!choice || !choice.enabled) {
                return true;
            }
            if (!choice.eligible) {
                return true;
            }
            if (choice.locked) {
                return true;
            }
            // Allow candidateId = 0 (kotak kosong) as a valid selection
            return choice.selected !== undefined && choice.selected !== null;
        },

        updateStepUI() {
            const current = this.state.step;

            this.stepperItems.each((_, element) => {
                const item = $(element);
                const step = Number(item.data('step'));
                item.toggleClass('active', step === current);
                item.toggleClass('completed', step < current);
            });

            this.stepPanes.each((_, element) => {
                const pane = $(element);
                const step = Number(pane.data('stepPane'));
                pane.toggleClass('active', step === current);
            });

            this.updateNavState();
            this.refreshSummary();
        },

        updateNavState() {
            const isFirstStep = this.state.step === 1;
            this.prevBtn.prop('disabled', isFirstStep || this.state.submitting);

            if (this.state.submitting) {
                this.nextBtn.text('Mengirim...');
                this.nextBtn.prop('disabled', true);
                return;
            }

            if (this.state.step === this.state.totalSteps) {
                this.nextBtn.text(this.hasPendingSubmission() ? 'Kirim Suara' : 'Selesai');
            } else {
                this.nextBtn.text('Lanjutkan');
            }

            let disableNext = false;
            if (this.state.step === 1) {
                disableNext = !this.isStepSatisfied('presbem');
            } else if (this.state.step === 2) {
                disableNext = !this.isStepSatisfied('caleg');
            }

            this.nextBtn.prop('disabled', disableNext);
        },

        hasPendingSubmission() {
            return ['presbem', 'caleg'].some((type) => this.shouldSubmit(type));
        },

        shouldSubmit(type) {
            const choice = this.state.choices[type];
            if (!choice || !choice.enabled) {
                return false;
            }
            if (!choice.eligible) {
                return false;
            }
            if (choice.locked) {
                return false;
            }
            // Allow candidateId = 0 (kotak kosong)
            if (choice.selected === undefined || choice.selected === null) {
                return false;
            }
            return String(choice.selected) !== String(choice.initial || '');
        },

        handleSubmit() {
            if (!this.hasPendingSubmission()) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Tindakan',
                    text: 'Tidak ada suara baru yang perlu dikirim.',
                });
                return;
            }

            const tasks = [];
            ['presbem', 'caleg'].forEach((type) => {
                if (this.shouldSubmit(type)) {
                    tasks.push(() => this.submitVote(type));
                }
            });

            this.state.submitting = true;
            this.updateNavState();

            tasks
                .reduce((promise, task) => promise.then(task), Promise.resolve())
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pilihanmu sudah terkirim.',
                    }).then(() => window.location.reload());
                })
                .catch((message) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message || 'Terjadi kesalahan, silakan coba lagi.',
                    });
                })
                .finally(() => {
                    this.state.submitting = false;
                    this.updateNavState();
                });
        },

        submitVote(type) {
            const choice = this.state.choices[type];
            if (!choice.voteUrl) {
                return Promise.reject('URL pemilihan tidak ditemukan.');
            }

            // Handle kotak kosong (candidate_id = 0 means null)
            const candidateId = String(choice.selected) === '0' ? null : choice.selected;

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: choice.voteUrl,
                    type: 'POST',
                    data: {
                        candidate_id: candidateId,
                        is_golput: candidateId === null ? 1 : 0,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: () => resolve(),
                    error: (xhr) => {
                        let message = 'Terjadi kesalahan, silakan coba lagi.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        reject(message);
                    },
                });
            });
        },

        refreshSummary() {
            ['presbem', 'caleg'].forEach((type) => {
                const choice = this.state.choices[type];
                const summaryName = this.root.find(`[data-summary-name="${type}"]`);
                const summaryDetail = this.root.find(`[data-summary-detail="${type}"]`);
                if (!summaryName.length || !summaryDetail.length) {
                    return;
                }

                if (!choice.eligible) {
                    summaryName.text('Tidak diwajibkan');
                    summaryDetail.text('Prodi kamu tidak tercantum dalam dapil aktif.');
                    return;
                }

                // Use choice.selected if defined (including 0), otherwise use choice.initial
                const candidateId = choice.selected !== undefined && choice.selected !== null
                    ? choice.selected
                    : choice.initial;

                if (!choice.enabled) {
                    summaryName.text('Tidak ada pemilihan aktif');
                    summaryDetail.text('Belum ada jadwal yang diumumkan.');
                    return;
                }

                // Check if candidateId is undefined or null (not selected yet)
                if (candidateId === undefined || candidateId === null) {
                    summaryName.text('Belum dipilih');
                    summaryDetail.text(
                        type === 'presbem'
                            ? 'Silakan memilih calon presiden BEM.'
                            : 'Langkah ini dapat dilewati jika bukan dapilmu.'
                    );
                    return;
                }

                const meta = (this.optionMap[type] && this.optionMap[type][String(candidateId)]) || {};
                const labelParts = [];
                if (meta.nomor) {
                    labelParts.push(`No. ${meta.nomor}`);
                }
                if (meta.name) {
                    labelParts.push(meta.name);
                }

                summaryName.text(labelParts.join(' - ') || 'Pilihan tersimpan');
                summaryDetail.text(meta.description || 'Suara kamu sudah tercatat.');
            });

            if (!this.summaryNote.length) {
                return;
            }

            if (this.hasPendingSubmission()) {
                this.summaryNote
                    .removeClass('alert-success')
                    .addClass('alert-info')
                    .text('Kirim suara sekarang. Sistem akan mencatat pilihanmu secara permanen.');
            } else {
                this.summaryNote
                    .removeClass('alert-info')
                    .addClass('alert-success')
                    .text('Pilihanmu sudah lengkap. Tidak ada suara baru yang perlu dikirim.');
            }
        },
    };

    $(document).ready(() => {
        PemilihanStepper.init();
    });
})(jQuery);
