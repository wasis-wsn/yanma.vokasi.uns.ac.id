<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Surat Keterangan Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('suketAlumni.store')}}" method="POST" id="form-tambah">
                @csrf
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="permohonan">Permohonan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="permohonan" name="permohonan" rows="3" placeholder="Tuliskan keperluan/tujuan permohonan surat..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nomor_ijazah">Nomor Ijazah <span class="text-danger">*</span></label>
                        <input class="form-control" id="nomor_ijazah" type="text" name="nomor_ijazah" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tanggal_lulus">Tanggal Lulus <span class="text-danger">*</span></label>
                        <input class="form-control" id="tanggal_lulus" type="date" name="tanggal_lulus" required>
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
