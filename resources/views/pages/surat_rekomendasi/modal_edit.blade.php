<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Surat Rekomendasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="nomor_ijazah-revisi">Nomor Ijazah <span class="text-danger">*</span></label>
                        <input class="form-control" id="nomor_ijazah-revisi" type="text" name="nomor_ijazah" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tanggal_lulus-revisi">Tanggal Lulus <span class="text-danger">*</span></label>
                        <input class="form-control" id="tanggal_lulus-revisi" type="date" name="tanggal_lulus" required>
                    </div>
                    <div class="form-group">
                        <label for="customFile1-revisi" class="form-label custom-file-input">Upload File (PDF)</label>
                        <input class="form-control" type="file" id="customFile1-revisi" name="file" accept="application/pdf">
                        <small class="text-danger"><i class="fa fa-warning"></i> Jangan Upload Apapun Jika File Tidak Direvisi atau Diedit!</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
