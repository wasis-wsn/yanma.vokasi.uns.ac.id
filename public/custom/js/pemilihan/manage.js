(function ($) {
    const routes = window.PemiluRoutes || {};
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const pemilihanModal = document.getElementById('modalPemilihan') ? new bootstrap.Modal(document.getElementById('modalPemilihan')) : null;
    const candidateModal = document.getElementById('modalCandidate') ? new bootstrap.Modal(document.getElementById('modalCandidate')) : null;

    let selectedPemilihanId = null;

    const pemilihanTable = $('#pemilihan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: routes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name', name: 'name' },
            { data: 'jenis_label', name: 'jenis' },
            { data: 'periode', name: 'mulai_at' },
            { data: 'status_badge', name: 'is_active', orderable: false, searchable: false },
            { data: 'candidates_count', name: 'candidates_count' },
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
        $('#form-candidate')[0].reset();
        $('#candidate_id').val('');
        $('#modalCandidateLabel').text('Tambah Calon');
        $('#btn-save-candidate').text('Simpan');
    }

    function updateSelectedPemilihanLabel(name = null) {
        const label = $('#selected-pemilihan-label');
        if (!name) {
            label.text('Pilih pemilihan untuk melihat calon.');
            $('.btn-add-candidate').prop('disabled', true);
            $('#candidate-table tbody').html('<tr><td colspan="4" class="text-center text-muted">Belum ada pemilihan yang dipilih.</td></tr>');
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
                updateSelectedPemilihanLabel(response.pemilihan.name);
            }

            if (!response.candidates.length) {
                tbody.append('<tr><td colspan="4" class="text-center text-muted">Belum ada calon.</td></tr>');
                return;
            }

            response.candidates.forEach((candidate, index) => {
                const visi = candidate.visi ? `<div class="small text-muted mb-1">Visi: ${candidate.visi}</div>` : '';
                const misi = candidate.misi ? `<div class="small text-muted">Misi: ${candidate.misi}</div>` : '';
                const detail = `<div class="fw-semibold text-dark">${candidate.nomor_urut}. ${candidate.name}</div>${visi}${misi}`;

                const action = `
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-warning btn-edit-candidate" data-id="${candidate.id}"><i class="fa fa-pen"></i></button>
                        <button class="btn btn-danger btn-delete-candidate" data-id="${candidate.id}"><i class="fa fa-trash"></i></button>
                    </div>
                `;

                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${detail}</td>
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
                    $('#pemilihan_id').val(response.data.id);
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

    $('#pemilihan-table').on('click', '.btn-toggle-pemilihan', function () {
        const id = $(this).data('id');
        const url = routes.toggle.replace(':id', id);

        $.ajax({
            url,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
            .done((response) => {
                Swal.fire('Berhasil', response.message, 'success');
                pemilihanTable.ajax.reload(null, false);
            })
            .fail(handleAjaxError);
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
                    $('#candidate_visi').val(response.data.visi);
                    $('#candidate_misi').val(response.data.misi);
                    $('#candidate_deskripsi').val(response.data.deskripsi);
                    $('#candidate_foto').val(response.data.foto);
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
        const formData = $(this).serialize();

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
            .done((response) => {
                Swal.fire('Berhasil', response.message, 'success');
                if (candidateModal) candidateModal.hide();
                loadCandidates(selectedPemilihanId);
            })
            .fail(handleAjaxError);
    });
})(jQuery);
