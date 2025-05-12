<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Surat Tugas Delegasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="catatan-revisi">Catatan Revisi:</label>
                        <textarea class="form-control" id="catatan-revisi" rows="2" disabled></textarea>
                    </div>
                    <p>
                        Silahkan upload Surat Pengantar Prodi, ToR RAB (jika mengajukan dana ke SV), 
                        Lembar Pernyataan LPJ/SPJ, dan Pamflet/Undangan/Pengumuman/Pedoman Lomba 
                        dalam 1 file dengan format file pdf
                    </p>
                    <div class="form-group">
                        <label for="edit_customFile1" class="form-label custom-file-input">
                            Unggah file PDF disini <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" type="file" id="edit_customFile1" name="file" accept="application/pdf">
                        <small class="text-danger"><i class="fa fa-warning"></i> Jangan Upload Apapun Jika File Tidak Direvisi / Diubah!</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_nama_kegiatan">Nama Kegiatan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_nama_kegiatan" type="text" name="nama_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_mulai_kegiatan">Tanggal Mulai Kegiatan <span class="text-danger">*</span>:</label>
                        <input type="date" class="form-control" id="edit_mulai_kegiatan" name="mulai_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_selesai_kegiatan">Tanggal Selesai Kegiatan <span class="text-danger">*</span>:</label>
                        <input type="date" class="form-control" id="edit_selesai_kegiatan" name="selesai_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_penyelenggara">Penyelenggara Kegiatan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_penyelenggara" type="text" name="penyelenggara">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_tempat">Tempat Pelaksanaan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_tempat" type="text" name="tempat">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_delegasi">Delegasi sebagai <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_delegasi" type="text" name="delegasi">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_jumlah_peserta">Jumlah Peserta Delegasi <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_jumlah_peserta" type="text" name="jumlah_peserta">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_dospem">Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_dospem" type="text" name="dospem">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_nip_dospem">NIP/NIK Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_nip_dospem" type="text" name="nip_dospem">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_nidn_dospem">NIDN Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_nidn_dospem" type="text" name="nidn_dospem">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_unit_dospem">Unit Kerja Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_unit_dospem" type="text" name="unit_dospem">
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