<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Surat Keterangan Lulus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('skl.store')}}" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label for="lembar_revisi" class="form-label custom-file-input">
                            Lembar Persetujuan Revisi Tugas Akhir yang sudah selesai (Sudah ditandatangani Dosen Penguji) 
                            <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" type="file" id="lembar_revisi" name="lembar_revisi" accept="application/pdf">
                        <small>Upload dalam bentuk PDF. Max 1 MB</small>
                    </div>
                    <div class="form-group">
                        <label for="ss_ajuan_skl" class="form-label custom-file-input">
                            Screenshot Bukti Ajuan SKL di SIAKAD 
                            <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" type="file" id="ss_ajuan_skl" name="ss_ajuan_skl" accept="image/png, image/jpeg">
                        <small>Upload dalam bentuk .png, .jpg, .jpeg. Max 1 MB</small>
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