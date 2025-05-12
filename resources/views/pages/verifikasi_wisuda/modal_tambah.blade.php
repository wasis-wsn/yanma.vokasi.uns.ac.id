<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalTambah">Buat Ajuan Verifikasi Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <p>
                        Scan semua berkas dibawah ini dan gabungkan dalam satu file .PDF: 
                        <ol>
                            <li>
                                Surat Keterangan Lulus (SKL)
                            </li>
                            <li>
                                Ijazah Terakhir
                            </li>
                            <li>
                                Pas Foto 3x4 dan 4x6
                            </li>
                            <li>
                                TOEFL/TOEIC
                            </li>
                            <li>
                                KHS Terakhir
                            </li>
                            <li>
                                Biaya Pendidikan (SPP) / Uang Kuliah Tunggal 
                                / Kwitansi Pembayaran SPP semester terakhir (dapat didownload melalui SIAKAD)
                            </li>
                            <li>
                                KTP
                            </li>
                            <li>
                                Bukti Penyerahan Skripsi / Tugas Akhir
                            </li>
                            <li>
                                Surat Bebas Kopma
                            </li>
                            <li>
                                Surat Bebas Perpustakaan
                            </li>
                            <li>
                                Surat Bebas Laboratorium (Jika Ada)
                            </li>
                            <li>
                                Surat Keterangan Perpanjangan Masa Studi (Jika Ada)
                            </li>
                            <li>
                                Surat Keterangan Selang / Cuti (Jika Ada)
                            </li>
                            <li>
                                E-Journal (Jika Ada)
                            </li>
                        </ol>
                    </p>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">Unggah File PDF <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                        <small class="text-danger">Ukuran Maksimal 100 MB</small>
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