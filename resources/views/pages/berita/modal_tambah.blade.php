<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label for="judul" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="gambar" class="form-label">Gambar Berita <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="gambar" name="gambar" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="PDF">File PDF</label>
                        <input type="file" class="form-control" id="PDF" name="PDF" accept="application/PDF">
                        <small class="text-muted">Ukuran maksimal 10MB</small>
                    </div>
                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" id="tanggal" name="tanggal" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
