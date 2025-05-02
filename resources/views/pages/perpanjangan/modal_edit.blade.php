<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Perpanjangan Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="catatan-revisi">Catatan Revisi:</label>
                        <textarea class="form-control" id="catatan-revisi" rows="2" disabled></textarea>
                    </div>
                    <p>
                        Scan dokumen berikut dan jadikan satu file .pdf: 
                        <ol>
                            <li>
                                Surat Pernyataan bermaterai dari mahasiswa (halaman ke-1 surat yang diunduh dari SIAKAD ketika ajuan)
                            </li>
                            <li>
                                Surat Permohonan dari mahasiswa yang ditujukan ke Dekan Sekolah Vokasi (halaman ke-2 surat yang diunduh dari SIAKAD ketika ajuan)
                            </li>
                            <li>
                                KHS Semester terakhir (download dari SIAKAD)
                            </li>
                            <li>
                                Bukti Kuitansi Pembayaran (download dari SIAKAD)
                            </li>
                        </ol>
                    </p>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">Unggah File PDF disini</label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                        <small class="text-danger">Jangan upload apapun jika file tidak direvisi/diubah</small>
                    </div>
                    {{-- <div class="form-group">
                        <label class="form-label" for="edit_semester">Perpanjangan Semester <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="edit_semester" type="text" name="semester" readonly>
                    </div> --}}
                    <div class="form-group">
                        <label class="form-label" for="edit_tahun_akademik">Tahun Akademik<span class="text-danger">*</span>:</label>
                        <input class="form-control" type="text" id="edit_tahun_akademik" @disabled(auth()->user()->role == '1')>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_semester">Semester Akademik<span class="text-danger">*</span>:</label>
                        <input class="form-control" type="text" id="edit_semester" @disabled(auth()->user()->role == '1')>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_perpanjangan_ke">Perpanjangan Ke <span class="text-danger">*</span>:</label>
                        <select name="perpanjangan_ke" id="edit_perpanjangan_ke" class="selectpicker form-control" data-style="py-0">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
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