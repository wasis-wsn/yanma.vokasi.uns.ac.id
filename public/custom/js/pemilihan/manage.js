(function ($) {
    const routes = window.PemiluRoutes || {};
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const pemilihanModal = document.getElementById('modalPemilihan') ? new bootstrap.Modal(document.getElementById('modalPemilihan')) : null;
    const candidateModal = document.getElementById('modalCandidate') ? new bootstrap.Modal(document.getElementById('modalCandidate')) : null;

    let selectedPemilihanId = null;
    let selectedPemilihanJenis = null;

    const candidatePhotoPreview = $('#candidate_foto_preview');
    const candidatePhotoPlaceholder = $('#candidate_foto_placeholder');
    const candidatePhotoInput = $('#candidate_foto');
    const candidateDapilGroup = $('#candidate_dapil_group');
    const candidateDapilCheckboxes = candidateDapilGroup.find('.dapil-prodi-checkbox');
    const dapilHelperText = $('#dapil_helper_text');
    const leaderInputs = {
        ketua_nama: $('#ketua_nama'),
        ketua_prodi: $('#ketua_prodi'),
        ketua_angkatan: $('#ketua_angkatan'),
        wakil_nama: $('#wakil_nama'),
        wakil_prodi: $('#wakil_prodi'),
        wakil_angkatan: $('#wakil_angkatan'),
    };

    const pemilihanTable = $('#pemilihan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: routes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'jenis_label', name: 'jenis' },
            { data: 'periode', name: 'mulai_at' },
            { data: 'status_badge', name: 'is_active', orderable: false, searchable: false },
            { data: 'candidates_count', name: 'candidates_count', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
    });

    function formatDatetimeInput(value) {
        if (!value) return '';
        return value.replace(' ', 'T').substring(0, 16);
    }

    function resetPemilihanForm() {
        $('#form-pemilihan')[0].reset();
        $('#pemilihan_id').val('');
        $('#modalPemilihanLabel').text('Tambah Pemilihan');
        $('#btn-save-pemilihan').text('Simpan');
    }

    function resetCandidateForm() {
        const form = $('#form-candidate')[0];
        form.reset();
        $('#candidate_id').val('');
        candidateDapilCheckboxes.prop('checked', false);
        Object.values(leaderInputs).forEach((input) => {
            if (input.is('select')) {
                input.prop('selectedIndex', 0);
            } else {
                input.val('');
            }
        });
        candidatePhotoInput.val('');
        setPhotoPreview(null);
        $('#modalCandidateLabel').text('Tambah Calon');
        $('#btn-save-candidate').text('Simpan');
        updateDapilFieldState();
    }

    function setPhotoPreview(url) {
        if (url) {
            candidatePhotoPreview.attr('src', url).removeClass('d-none');
            candidatePhotoPlaceholder.addClass('d-none');
        } else {
            candidatePhotoPreview.attr('src', '').addClass('d-none');
            candidatePhotoPlaceholder.removeClass('d-none');
        }
    }

    function setSelectValue(selectEl, value) {
        if (!selectEl || !selectEl.length) {
            return;
        }
        if (!value) {
            selectEl.val('');
            return;
        }
        if (!selectEl.find(`option[value="${value}"]`).length) {
            selectEl.append(new Option(value, value));
        }
        selectEl.val(value);
    }

    function fillLeaderFields(payload = {}) {
        Object.entries(leaderInputs).forEach(([key, input]) => {
            if (input.is('select')) {
                setSelectValue(input, payload[key] || '');
            } else {
                input.val(payload[key] || '');
            }
        });
    }

    function renderLeaderLine(label, name, prodi, angkatan) {
        const metaParts = [];
        if (prodi) {
            metaParts.push(prodi);
        }
        if (angkatan) {
            metaParts.push(`Angkatan ${angkatan}`);
        }
        const meta = metaParts.length ? ` <span class="text-muted">(${metaParts.join(' • ')})</span>` : '';
        return `<div class="small text-muted"><strong>${label}:</strong> ${name || '-'}${meta}</div>`;
    }

    function updateDapilFieldState() {
        const isCaleg = selectedPemilihanJenis === 'caleg';
        candidateDapilGroup.toggleClass('d-none', !isCaleg);
        candidateDapilCheckboxes.prop('disabled', !isCaleg);
        if (!isCaleg) {
            candidateDapilCheckboxes.prop('checked', false);
        }
        if (dapilHelperText.length) {
            dapilHelperText.text(
                isCaleg
                    ? 'Pilih satu atau beberapa prodi dapil untuk calon legislatif.'
                    : 'Dapil hanya perlu diisi untuk pemilihan legislatif.'
            );
        }
    }

    function updateSelectedPemilihanLabel(name = null) {
        const label = $('#selected-pemilihan-label');
        if (!name) {
            label.text('Pilih pemilihan untuk melihat calon.');
            $('.btn-add-candidate').prop('disabled', true);
            $('#candidate-table tbody').html('<tr><td colspan="5" class="text-center text-muted">Belum ada pemilihan yang dipilih.</td></tr>');
            selectedPemilihanJenis = null;
            updateDapilFieldState();
            return;
        }
        label.text(`Pemilihan: ${name}`);
        $('.btn-add-candidate').prop('disabled', false);
    }

    function loadCandidates(pemilihanId) {
        if (!pemilihanId) return;
        const url = routes.candidateList.replace(':id', pemilihanId);
        $.ajax({
            url,
            type: 'GET',
        }).done((response) => {
            const tbody = $('#candidate-table tbody');
            tbody.empty();

            if (response.pemilihan) {
                selectedPemilihanJenis = response.pemilihan.jenis || null;
                updateSelectedPemilihanLabel(response.pemilihan.name);
            } else {
                selectedPemilihanJenis = null;
                updateSelectedPemilihanLabel();
            }

            updateDapilFieldState();

            const candidates = Array.isArray(response.candidates) ? response.candidates : [];

            if (!candidates.length) {
                tbody.append('<tr><td colspan="5" class="text-center text-muted">Belum ada calon.</td></tr>');
                return;
            }

            candidates.forEach((candidate, index) => {
                const visi = candidate.visi ? `<div class="small text-muted mb-1">Visi: ${candidate.visi}</div>` : '';
                const misi = candidate.misi ? `<div class="small text-muted">Misi: ${candidate.misi}</div>` : '';
                const ketuaLine = renderLeaderLine('Ketua', candidate.ketua_nama, candidate.ketua_prodi, candidate.ketua_angkatan);
                const wakilLine = renderLeaderLine('Wakil', candidate.wakil_nama, candidate.wakil_prodi, candidate.wakil_angkatan);
                const detailText = `<div class="fw-semibold text-dark mb-1">${candidate.nomor_urut}. ${candidate.name}</div>${ketuaLine}${wakilLine}${visi}${misi}`;

                const photoHtml = candidate.photo_url
                    ? `<img src="${candidate.photo_url}" class="rounded border" style="width:72px;height:72px;object-fit:cover;" alt="Foto ${candidate.name}" loading="lazy">`
                    : '<div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width:72px;height:72px;"><i class="fa-solid fa-user"></i></div>';

                const dapilBadges = candidate.dapil_names && candidate.dapil_names.length
                    ? candidate.dapil_names.map((name) => `<div class="d-block mb-1"><span class="badge bg-primary text-white">${name}</span></div>`).join('')
                    : '<span class="text-muted small d-block">Belum ada dapil.</span>';

                const action = `
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-warning btn-edit-candidate" data-id="${candidate.id}"><i class="fa fa-pen"></i></button>
                        <button class="btn btn-danger btn-delete-candidate" data-id="${candidate.id}"><i class="fa fa-trash"></i></button>
                    </div>
                `;

                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-start gap-3">
                                <div>${photoHtml}</div>
                                <div>${detailText}</div>
                            </div>
                        </td>
                        <td>
                            <div>${dapilBadges}</div>
                        </td>
                        <td><span class="fw-semibold">${candidate.total_votes}</span></td>
                        <td class="text-end">${action}</td>
                    </tr>
                `);
            });
        });
    }

    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan, silakan coba lagi.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }
        Swal.fire('Gagal', message, 'error');
    }

    updateDapilFieldState();

    if (candidatePhotoInput.length) {
        candidatePhotoInput.on('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                setPhotoPreview(null);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => setPhotoPreview(e.target.result);
            reader.readAsDataURL(file);
        });
    }

    $('.btn-add-pemilihan').on('click', function () {
        resetPemilihanForm();
        if (pemilihanModal) {
            pemilihanModal.show();
        }
    });

    $('#pemilihan-table').on('click', '.btn-edit-pemilihan', function () {
        const id = $(this).data('id');
        const url = routes.show.replace(':id', id);

        $.ajax({ url, type: 'GET' })
            .done((response) => {
                if (response.data) {
                    resetPemilihanForm();
                    $('#pemilihan_id').val(response.data.slug);
                    $('#pemilihan_name').val(response.data.name);
                    $('#pemilihan_jenis').val(response.data.jenis);
                    $('#pemilihan_mulai').val(formatDatetimeInput(response.data.mulai_at));
                    $('#pemilihan_selesai').val(formatDatetimeInput(response.data.selesai_at));
                    $('#pemilihan_deskripsi').val(response.data.deskripsi);
                    $('#pemilihan_active').prop('checked', Boolean(response.data.is_active));
                    $('#modalPemilihanLabel').text('Edit Pemilihan');
                    $('#btn-save-pemilihan').text('Perbarui');
                    if (pemilihanModal) pemilihanModal.show();
                }
            })
            .fail(handleAjaxError);
    });

    $('.btn-toggle-all-menu').on('click', function () {
        const currentText = $(this).text().trim();
        const action = currentText.includes('Sembunyikan') ? 'menyembunyikan' : 'menampilkan';

        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${action} menu pemilu di dashboard mahasiswa?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.toggleAllMenu,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                })
                    .done((response) => {
                        Swal.fire('Berhasil', response.message, 'success').then(() => {
                            // Reload halaman untuk update button state
                            window.location.reload();
                        });
                    })
                    .fail(handleAjaxError);
            }
        });
    });

    $('#pemilihan-table').on('click', '.btn-delete-pemilihan', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Pemilihan?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const url = routes.delete.replace(':id', id);
                $.ajax({
                    url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                })
                    .done((response) => {
                        Swal.fire('Berhasil', response.message, 'success');
                        if (selectedPemilihanId === id) {
                            selectedPemilihanId = null;
                            updateSelectedPemilihanLabel();
                        }
                        pemilihanTable.ajax.reload(null, false);
                    })
                    .fail(handleAjaxError);
            }
        });
    });

    $('#pemilihan-table').on('click', '.btn-manage-candidate', function () {
        selectedPemilihanId = $(this).data('id');
        loadCandidates(selectedPemilihanId);
    });

    $('#form-pemilihan').on('submit', function (e) {
        e.preventDefault();
        const pemilihanId = $('#pemilihan_id').val();
        const url = pemilihanId ? routes.update.replace(':id', pemilihanId) : routes.store;
        const formData = $(this).serialize();

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
            .done((response) => {
                Swal.fire('Berhasil', response.message, 'success');
                if (pemilihanModal) pemilihanModal.hide();
                pemilihanTable.ajax.reload(null, false);
                if (selectedPemilihanId) {
                    loadCandidates(selectedPemilihanId);
                }
            })
            .fail(handleAjaxError);
    });

    $('.btn-add-candidate').on('click', function () {
        if (!selectedPemilihanId) {
            Swal.fire('Informasi', 'Pilih pemilihan terlebih dahulu.', 'info');
            return;
        }
        resetCandidateForm();
        if (candidateModal) candidateModal.show();
    });

    $('#candidate-table').on('click', '.btn-edit-candidate', function () {
        const id = $(this).data('id');
        const url = routes.candidateShow.replace(':id', id);
        $.ajax({
            url,
            type: 'GET',
        })
            .done((response) => {
                if (response.data) {
                    resetCandidateForm();
                    $('#candidate_id').val(response.data.id);
                    $('#candidate_nomor').val(response.data.nomor_urut);
                    $('#candidate_name').val(response.data.name);
                    fillLeaderFields(response.data);
                    $('#candidate_visi').val(response.data.visi);
                    $('#candidate_misi').val(response.data.misi);
                    $('#candidate_deskripsi').val(response.data.deskripsi);
                    const dapilIds = (response.data.dapil_prodi_ids || []).map((id) => Number(id));
                    candidateDapilCheckboxes.each(function () {
                        const checkbox = $(this);
                        checkbox.prop('checked', dapilIds.includes(Number(checkbox.val())));
                    });
                    setPhotoPreview(response.data.photo_url || null);
                    updateDapilFieldState();
                    $('#modalCandidateLabel').text('Edit Calon');
                    $('#btn-save-candidate').text('Perbarui');
                    if (candidateModal) candidateModal.show();
                }
            })
            .fail(handleAjaxError);
    });

    $('#candidate-table').on('click', '.btn-delete-candidate', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Calon?',
            text: 'Data calon akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const url = routes.candidateDelete.replace(':id', id);
                $.ajax({
                    url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                })
                    .done((response) => {
                        Swal.fire('Berhasil', response.message, 'success');
                        loadCandidates(selectedPemilihanId);
                    })
                    .fail(handleAjaxError);
            }
        });
    });

    $('#form-candidate').on('submit', function (e) {
        e.preventDefault();
        if (!selectedPemilihanId) {
            Swal.fire('Informasi', 'Pilih pemilihan terlebih dahulu.', 'info');
            return;
        }
        const candidateId = $('#candidate_id').val();
        const baseUrl = candidateId ? routes.candidateUpdate.replace(':id', candidateId) : routes.candidateStore.replace(':id', selectedPemilihanId);
        const formElement = document.getElementById('form-candidate');
        const formData = new FormData(formElement);

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            processData: false,
            contentType: false,
        })
            .done((response) => {
                Swal.fire('Berhasil', response.message, 'success');
                if (candidateModal) candidateModal.hide();
                loadCandidates(selectedPemilihanId);
            })
            .fail(handleAjaxError);
    });
})(jQuery);
