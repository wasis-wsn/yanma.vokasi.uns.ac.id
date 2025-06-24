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
                    <div class="mb-3">
                        <p id="konfirmasi-text" class="fw-bold"></p>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_mahasiswa" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan_mahasiswa" id="catatan_mahasiswa" rows="3" 
                            placeholder="Tuliskan catatan jika diperlukan..."></textarea>
                    </div>
                    <input type="hidden" name="konfirmasi" id="konfirmasi_value">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="btn-konfirmasi-submit">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
