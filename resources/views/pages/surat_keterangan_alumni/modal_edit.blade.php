<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Surat Keterangan Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    @can('mahasiswa')
                    <!-- Form untuk Mahasiswa -->
                    <div class="form-group">
                        <label class="form-label" for="permohonan-revisi">Permohonan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="permohonan-revisi" name="permohonan" rows="3" placeholder="Tuliskan keperluan/tujuan permohonan surat..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nomor_ijazah-revisi">Nomor Ijazah <span class="text-danger">*</span></label>
                        <input class="form-control" id="nomor_ijazah-revisi" type="text" name="nomor_ijazah" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tanggal_lulus-revisi">Tanggal Lulus <span class="text-danger">*</span></label>
                        <input class="form-control" id="tanggal_lulus-revisi" type="date" name="tanggal_lulus" required>
                    </div>
                    @endcan

                    @canany(['staff','dekanat','subkoor','adminprodi','fo'])
                    <!-- Form untuk Staff - Hanya Edit Status -->
                    <div class="form-group">
                        <label class="form-label" for="status_id-revisi">Status Ajuan <span class="text-danger">*</span></label>
                        <select class="form-control" id="status_id-revisi" name="status_id" required>
                            <option value="">-- Pilih Status --</option>
                            @foreach($status as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="form-no-surat-edit" hidden>
                        <label class="form-label" for="no_surat-revisi">Nomor Surat <span class="text-danger">*</span></label>
                        <input class="form-control" id="no_surat-revisi" type="text" name="no_surat">
                    </div>
                    <div class="form-group" id="form-file-edit" hidden>
                        <label class="form-label" for="file-revisi">Upload File Final <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="file-revisi" name="file" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        <small class="text-muted">Unggah dokumen final ketika status diselesaikan.</small>
                    </div>
                    @endcanany
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
