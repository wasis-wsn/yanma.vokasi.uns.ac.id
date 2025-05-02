<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pembina Ormawa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label for="name" class="form-label custom-file-input">Nama <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="nip" class="form-label custom-file-input">NIP/NIK <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="nip" name="nip">
                    </div>
                    <div class="form-group">
                        <label for="nidn" class="form-label custom-file-input">NIDN <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="nidn" name="nidn">
                    </div>
                    <div class="form-group" id="div_unit_id">
                        <label for="unit_id" class="form-label custom-file-input">Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-control">
                            <option value="" selected></option>
                        </select>
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