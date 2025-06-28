{{-- Modal Konfirmasi --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKonfirmasiLabel">Konfirmasi Keikutsertaan Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-konfirmasi" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="konfirmasi-text">Apakah Anda yakin bersedia mengikuti wisuda pada periode ini?</p>

                    <input type="hidden" name="konfirmasi" id="konfirmasi_value">

                    <div class="mb-3" id="catatan-field">
                        <label for="catatan" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" name="catatan" id="catatan" rows="3"
                            placeholder="Jelaskan alasan Anda menolak untuk mengikuti wisuda..."></textarea>
                        <small class="text-muted">Catatan ini akan membantu staff memahami keputusan Anda.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btn-konfirmasi-submit">Ya, Saya Setuju</button>
                </div>
            </form>
        </div>
    </div>
</div>
