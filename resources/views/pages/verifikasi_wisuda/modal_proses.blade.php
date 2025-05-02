<div class="modal fade" id="modalProses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalProses">Proses Ajuan Verifikasi Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-proses" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="status_id">Status Verifikasi <span class="text-danger">*</span></label>
                        <select class="form-select" data-trigger name="status_id" id="status_id">
                            @foreach ($status as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-v9">
                        <label class="form-label" for="no_seri_ijazah">No Seri Ijazah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_seri_ijazah" name="no_seri_ijazah">
                    </div>
                    <div class="form-group form-v9">
                        <label class="form-label" for="periode_wisuda">Periode Wisuda <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="periode_wisuda" name="periode_wisuda">
                    </div>
                    <div class="form-group form-v9">
                        <label class="form-label" for="kode_akses">Kode Akses Wisuda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode_akses" name="kode_akses">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="catatan">Catatan: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="catatan" rows="2" name="catatan"></textarea>
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