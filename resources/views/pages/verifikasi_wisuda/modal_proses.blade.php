<div class="modal fade" id="modalProses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalProses">Proses Ajuan Verifikasi Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-proses" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-dark">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> Data mahasiswa dibawah tidak dapat diubah. Anda hanya dapat mengubah status dan menambahkan catatan.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="no_seri_ijazah">No Seri Ijazah</label>
                                <input type="text" class="form-control" id="no_seri_ijazah" name="no_seri_ijazah" readonly
                                       placeholder="Belum tersedia">
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="periode_wisuda">Periode Wisuda</label>
                                <input type="month" class="form-control" id="periode_wisuda" name="periode_wisuda" readonly>
                                <small class="text-muted">Data ini tidak dapat diubah</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group mb-3">
                        <label class="form-label" for="status_id">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status_id" id="status_id" required>
                            @foreach ($status as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih status yang sesuai untuk mahasiswa ini</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="catatan">Catatan untuk Mahasiswa</label>
                        <textarea class="form-control" id="catatan" rows="4" name="catatan"
                                  placeholder="Catatan akan terisi otomatis sesuai status yang dipilih..."></textarea>
                        <small class="text-muted">Catatan ini akan ditampilkan pada halaman verifikasi wisuda mahasiswa. Catatan akan otomatis terisi sesuai status yang dipilih, namun Anda dapat mengubahnya sesuai kebutuhan.</small>

                        {{-- Quick Action Buttons for Common Notes --}}

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
