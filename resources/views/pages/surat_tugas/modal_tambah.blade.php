<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Surat Tugas Delegasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('st.store')}}" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <p>
                        Silahkan upload Surat Pengantar Prodi, ToR RAB (jika mengajukan dana ke SV), 
                        Lembar Pernyataan LPJ/SPJ, dan Pamflet/Undangan/Pengumuman/Pedoman Lomba 
                        dalam 1 file dengan format file pdf
                    </p>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">
                            Unggah file PDF disini <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nama_kegiatan">Nama Kegiatan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="nama_kegiatan" type="text" name="nama_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mulai_kegiatan">Tanggal Mulai Kegiatan <span class="text-danger">*</span>:</label>
                        <input type="date" class="form-control" id="mulai_kegiatan" name="mulai_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="selesai_kegiatan">Tanggal Selesai Kegiatan <span class="text-danger">*</span>:</label>
                        <input type="date" class="form-control" id="selesai_kegiatan" name="selesai_kegiatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="penyelenggara">Penyelenggara Kegiatan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="penyelenggara" type="text" name="penyelenggara">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tempat">Tempat Pelaksanaan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="tempat" type="text" name="tempat">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="delegasi">Delegasi sebagai <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="delegasi" type="text" name="delegasi">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="jumlah_peserta">Jumlah Peserta Delegasi <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="jumlah_peserta" type="text" name="jumlah_peserta">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="dospem">Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="dospem" type="text" name="dospem">
                        <ul class="list-group" aria-labelledby="search-input" id="listDospem">
                            <!-- Isi dropdown akan diisi secara dinamis menggunakan JavaScript -->
                        </ul>
                        <div class="dropdown search-dropdown list-group" id="dospemDropdown">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nip_dospem">NIP/NIK Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="nip_dospem" type="text" name="nip_dospem">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nidn_dospem">NIDN Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="nidn_dospem" type="text" name="nidn_dospem">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="unit_dospem">Unit Kerja Dosen Pembimbing <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="unit_dospem" type="text" name="unit_dospem">
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