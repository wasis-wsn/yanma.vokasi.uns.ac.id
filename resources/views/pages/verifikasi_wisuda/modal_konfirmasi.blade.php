{{-- Modal Konfirmasi Setuju --}}
<div class="modal fade" id="modalKonfirmasiSetuju" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Kehadiran Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-konfirmasi-setuju" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        <strong>Konfirmasi:</strong> Anda menyatakan bersedia mengikuti wisuda dan wajib mengupload file konfirmasi.
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">File Konfirmasi <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".pdf" required>
                        <small class="text-muted">Format: PDF</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <input type="hidden" name="konfirmasi" value="setuju">
                    <input type="hidden" name="verifikasi_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-upload"></i> Upload & Konfirmasi Setuju
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Tidak Setuju --}}
<div class="modal fade" id="modalKonfirmasiTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Kehadiran Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-konfirmasi-tolak" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Anda menyatakan tidak bersedia mengikuti wisuda periode ini dan tetap wajib mengupload file konfirmasi.
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">File Konfirmasi <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".pdf" required>
                        <small class="text-muted">Format: PDF</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alasan/Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-control" placeholder="Jelaskan alasan Anda tidak bersedia mengikuti wisuda..."></textarea>
                    </div>

                    <input type="hidden" name="konfirmasi" value="tidak_setuju">
                    <input type="hidden" name="verifikasi_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-upload"></i> Upload & Konfirmasi Tidak Bersedia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
