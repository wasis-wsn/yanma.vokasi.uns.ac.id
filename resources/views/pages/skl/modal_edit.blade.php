<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Surat Keterangan Lulus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="lembar_pengesahan" class="form-label">
                            Catatan Reviewer
                        </label>
                        <textarea class="form-control" id="catatan-revisi" rows="3" name="catatan" readonly></textarea>
                        <small><span class="text-danger">*)</span> Jika Anda hanya disuruh mengganti Foto Profil di SIAKAD, Anda cukup klik tombol Kirim setelah mengganti Foto Profil</small>
                    </div>
                    <div class="form-group">
                        <label for="lembar_revisi" class="form-label custom-file-input">
                            Lembar Persetujuan Revisi Tugas Akhir yang sudah selesai (Sudah ditandatangani Dosen Penguji)
                        </label>
                        <input class="form-control" type="file" id="lembar_revisi" name="lembar_revisi" accept="application/pdf">
                        <small>Upload dalam bentuk PDF. Max 1 MB</small><br>
                        <small class="text-danger">Jangan upload apapun jika file tidak direvisi</small>
                    </div>
                    <div class="form-group">
                        <label for="ss_ajuan_skl" class="form-label custom-file-input">
                            Screenshot Bukti Ajuan SKL di SIAKAD
                        </label>
                        <input class="form-control" type="file" id="ss_ajuan_skl" name="ss_ajuan_skl" accept="image/png, image/jpeg">
                        <small>Upload dalam bentuk .png, .jpg, .jpeg. Max 1 MB</small><br>
                        <small class="text-danger">Jangan upload apapun jika file tidak direvisi</small>
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