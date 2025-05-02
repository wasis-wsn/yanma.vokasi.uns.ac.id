<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan SIK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_catatan">Catatan:</label>
                        <textarea class="form-control" id="edit_catatan" rows="2" disabled></textarea>
                    </div>
                    <p>
                        Proposal dan Lampiran dijadikan satu file dan diunggah dalam bentuk PDF
                    </p>
                    <div class="form-group">
                        <label for="edit_customFile1" class="form-label custom-file-input">Unggah Proposal dan Lampiran PDF disini</label>
                        <input class="form-control" type="file" id="edit_customFile1" name="file" accept="application/pdf">
                        <small class="text-danger">Jangan Upload apapun jika Proposal dan Lampiran tidak direvisi</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nama_ormawa">Nama Ormawa <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="nama_ormawa" type="text" value="{{ auth()->user()->name }}" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="pembina_ormawa">Pembina Ormawa <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="pembina_ormawa" type="text" value="{{ auth()->user()->pembina->name }}" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_nama_kegiatan">Nama Kegiatan <span class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="edit_nama_kegiatan" rows="2" name="nama_kegiatan"></textarea>
                    </div>
                    <div class="form-group" id="div_edit_ketua_id">
                        <label class="form-label" for="edit_ketua_id">Ketua Kegiatan <span class="text-danger">*</span>:</label>
                        <select name="ketua_id" id="edit_ketua_id" class="form-control">
                            <option value="" selected></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_no_surat_ormawa">Nomor Surat Ormawa <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_no_surat_ormawa" type="text" name="no_surat_ormawa">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_tanggal_surat">Tanggal Surat <span class="text-danger">*</span>:</label>
                        <input type="date" class="form-control" id="edit_tanggal_surat" name="tanggal_surat">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apakah Mengajukan Dana ke SV UNS?</label>
                        <div class="container">
                            <div class="d-flex">
                                <div class="row ml-2">
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="radio" value="1" id="edit_is_dana1" name="is_dana">
                                        <label class="form-check-label" for="is_dana1">
                                            Mengajukan Dana
                                        </label>
                                    </div>
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="radio" value="0" id="edit_is_dana0" name="is_dana">
                                        <label class="form-check-label" for="is_dana0">
                                            Tidak Mengajukan Dana
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_mulai_kegiatan">Tanggal Mulai Kegiatan <span class="text-danger">*</span>:</label>
                        <input type="datetime-local" class="form-control" id="edit_mulai_kegiatan" name="mulai_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_selesai_kegiatan">Tanggal Selesai Kegiatan <span class="text-danger">*</span>:</label>
                        <input type="datetime-local" class="form-control selesai_kegiatan" id="edit_selesai_kegiatan" name="selesai_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_tanggal_lpj">Tanggal Maksimal Penyerahan LPJ dan SPJ <span class="text-danger">*</span>:</label>
                        <input type="date" class="form-control tanggal_lpj" id="edit_tanggal_lpj" name="tanggal_lpj" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_tempat">Tempat Kegiatan <span class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="edit_tempat" rows="2" name="tempat"></textarea>
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