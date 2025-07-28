<div class="modal fade" id="modalTambahTA" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Ajuan TTD Lembar Pengesahan TA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah-ta" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group" id="div_mhs_id">
                        <label class="form-label" for="mhs_id">Mahasiswa<span class="text-danger">*</span></label>
                        <select name="user_id" id="mhs_id" class="selectpicker form-control" data-style="py-0">
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>