<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Ajuan Legalisir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <p>
                        Pastikan hal berikut bisa terbaca dan terlihat jelas pada dokumen milik pemohon:
                        <ol>
                            <li>Nama yang Bersangkutan</li>
                            <li>Nomor Ijazah/Transkrip</li>
                            <li>Foto</li>
                            <li>Nama, NIP, dan TTD Pejabat</li>
                            <li>Logo UNS</li>
                            <li>Stempel Pengesahan</li>
                            <li>Tabel Mata Kuliah</li>
                            <li>Tabel Tidak Miring</li>
                            <li>Kop Tidak Terpotong</li>
                            <li>Angka atau Huruf tidak terpotong</li>
                            <li>Ukuran Kertas Fotocopy sesuai dengan dokumen asli</li>
                        </ol>
                        Jika ada yang tidak sesuai, <span class="text-danger">jangan diajukan</span> dan pemohon bisa diarahkan untuk 
                        memperbaiki dokumen terlebih dahulu.
                    </p>
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap Alumni <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="name" type="text" name="name">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nim">NIM Alumni <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="nim" type="text" name="nim">
                    </div>
                    <div class="form-group" id="div_prodi_id">
                        <label class="form-label" for="prodi_id">Program Studi Alumni <span class="text-danger">*</span>:</label>
                        <select name="prodi_id" id="prodi_id" class="form-control" data-style="py-0" required>
                        </select>
                        <input class="form-control" type="text" name="namaprodi" id="namaProdi" hidden>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="no_wa">Nomor WhatsApp Alumni <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="no_wa" type="text" name="no_wa">
                        <small>Contoh : 08123456789</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Legalisir <span class="text-danger">*</span>:</label>
                        <div class="container">
                            <div class="d-flex">
                                <div class="row ml-2">
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="checkbox" value="Ijazah" name="legalisir[]" id="Ijazah">
                                        <label class="form-check-label" for="Ijazah">
                                            Ijazah
                                        </label>
                                    </div>
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="checkbox" value="Transkrip" name="legalisir[]" id="Transkrip">
                                        <label class="form-check-label" for="Transkrip">
                                            Transkrip
                                        </label>
                                    </div>
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="checkbox" value="Akreditasi" name="legalisir[]" id="Akreditasi">
                                        <label class="form-check-label" for="Akreditasi">
                                            Akreditasi
                                        </label>
                                    </div>
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="checkbox" value="Lainnya" name="legalisir[]" id="Lainnya">
                                        <label class="form-check-label" for="Lainnya">
                                            Lainnya
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="jumlah">Jumlah Legalisir (Maksimal 10 Lembar Keseluruhan) <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="jumlah" type="number" max="10" name="jumlah">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="keperluan">Keperluan <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="keperluan" type="text" name="keperluan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tahun_lulus">Tahun Lulus <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="tahun_lulus" type="number" min="1976" max="{{ date('Y') }}" name="tahun_lulus">
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