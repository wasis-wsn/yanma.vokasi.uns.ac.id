<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Prodi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('prodi.store')}}" method="POST" id="form-tambah">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">Nama Program Studi <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="customFile1" name="name">
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