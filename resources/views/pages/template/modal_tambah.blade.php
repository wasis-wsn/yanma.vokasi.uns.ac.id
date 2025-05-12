<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah">
                <div class="modal-body text-dark">
                    <div class="form-group" id="div_layanan_id">
                        <label for="layanan_id" class="form-label">Layanan <span class="text-danger">*</span></label>
                        <select name="layanan_id" id="layanan_id" class="form-control">
                            <option value="" selected></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="template" class="form-label">Template <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="template" name="template">
                    </div>
                    <div class="form-group">
                        <label for="file" class="form-label custom-file-input">File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="file" name="file">
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