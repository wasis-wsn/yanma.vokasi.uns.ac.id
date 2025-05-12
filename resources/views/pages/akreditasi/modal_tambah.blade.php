<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Akreditasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group" id="div_prodi_id">
                        <label class="form-label" for="prodi_id">Prodi<span class="text-danger">*</span></label>
                        <select name="prodi_id" id="prodi_id" class="selectpicker form-control" data-style="py-0">
                            @foreach ($prodis as $prodi)
                                <option value="{{$prodi->id}}">{{$prodi->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tahun" class="form-label custom-file-input">Tahun <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="tahun" name="tahun">
                    </div>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">File Akreditasi <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                        <small class="text-dager" id="infoCustomFile1" hidden>Jangan upload apapun jika tidak ada perubahan file</small>
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