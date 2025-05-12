<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Penundaan Pembayaran UKT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="catatan-revisi">Catatan Revisi:</label>
                        <textarea class="form-control" id="catatan-revisi" rows="2" disabled></textarea>
                    </div>
                    <p>
                        Scan dokumen berikut dan jadikan dalam satu file .pdf: 
                        <ol>
                            <li>
                                Surat Permohonan Penundaan
                            </li>
                            <li>
                                ScreenShoot Ajuan Perpanjangan (untuk mahasiswa D3 semester 7 keatas dan mahasiswa D4 semester 8 keatas)
                            </li>
                            <li>
                                KHS Terakhir
                            </li>
                            <li>
                                Bukti Kuitansi Pembayaran (download dari SIAKAD)
                            </li>
                        </ol>
                    </p>
                    <div class="form-group">
                        <label for="edit_customFile1" class="form-label custom-file-input">Dokumen Persyaratan</label>
                        <small class="text-danger">Ukuran Maks File 10 MB</small>
                        <input class="form-control" type="file" id="edit_customFile1" name="file" accept="application/pdf">
                        <small class="text-danger">Jangan upload apapun jika file tidak direvisi/diubah</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_tahun_akademik">Tahun Akademik<span class="text-danger">*</span>:</label>
                        <input class="form-control" type="text" id="edit_tahun_akademik" @disabled(auth()->user()->role == '1')>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_semester">Semester Akademik<span class="text-danger">*</span>:</label>
                        <input class="form-control" type="text" id="edit_semester" @disabled(auth()->user()->role == '1')>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_alasan">Alasan Penundaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_alasan" rows="2" name="alasan"></textarea>
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