<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Pembayaran UKT Diluar Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="catatan-revisi">Catatan Revisi:</label>
                        <textarea class="form-control" id="catatan-revisi" rows="2" disabled></textarea>
                    </div>
                    <p>
                        Scan dokumen persyaratan berikut dan jadikan satu file .pdf: 
                        <ol>
                            <li>
                                Surat Permohonan oleh mahasiswa yang ditujukan ke Dekan Sekolah Vokasi 
                                (menerangkan alasan telat pembayaran dan tanggal akan dibayarkan) 
                                sudah bertanda tangan dan menggunakan materai ASLI
                            </li>
                            <li>
                                KHS Semester terakhir (download dari SIAKAD)
                            </li>
                            <li>
                                Bukti Kuitansi Pembayaran (download dari SIAKAD)
                            </li>
                            <li>
                                Bagi mahasiswa semester akhir yang mendapatkan keringanan UKT TA silakan mengganti 
                                Riwayat Pembayaran UKT dengan SK Penetapan Mahasiswa Penerima Pembebasan UKT
                            </li>
                            <li>
                                Bukti Pembayaran UKT Terakhir. Mohon utamakan mengunggah scan bukti bayar asli dari bank, dan pastikan hasil scan JELAS. 
                                Apabila slip asli dari bank hilang, mahasiswa bisa mengunduh kuitansi dari Siakad.
                            </li>
                            <li>
                                Izin Cuti/Selang (bila ada)
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
                        <label class="form-label" for="edit_semester_romawi">Semester <span class="text-danger">*</span>:</label>
                        <select name="semester_romawi" id="edit_semester_romawi" class="selectpicker form-control" data-style="py-0">
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="VI">VI</option>
                            <option value="VII">VII</option>
                            <option value="VIII">VIII</option>
                            <option value="IX">IX</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                            <option value="XIII">XIII</option>
                            <option value="XIV">XIV</option>
                        </select>
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
                        <label class="form-label" for="edit_alasan">Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_alasan" rows="2" name="alasan"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_tanggal_bayar">Tanggal Akan Membayar <span class="text-danger">*</span></label>
                        <input class="form-control" id="edit_tanggal_bayar" type="date" name="tanggal_bayar">
                    </div>
                    {{-- <div class="form-group">
                        <label for="edit_bukti_bayar_ukt" class="form-label custom-file-input">Bukti Pembayaran UKT Terakhir <span class="text-danger">*</span></label>
                        <small class="text-danger">Ukuran Maks File 10 MB</small>
                        <br>
                        <small>
                            Mohon utamakan mengunggah scan bukti bayar asli dari bank, dan pastikan hasil scan JELAS. 
                            Apabila slip asli dari bank hilang, mahasiswa bisa mengunduh kuitansi dari Siakad.
                        </small>
                        <input class="form-control" type="file" id="edit_bukti_bayar_ukt" name="bukti_bayar_ukt" accept="application/pdf">
                        <small class="text-danger">Jangan upload apapun jika file tidak direvisi/diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_izin_cuti" class="form-label custom-file-input">Izin Cuti/Selang (bila ada)</label>
                        <small class="text-danger">Ukuran Maks File 10 MB</small>
                        <input class="form-control" type="file" id="edit_izin_cuti" name="izin_cuti" accept="application/pdf">
                        <small class="text-danger">Jangan upload apapun jika file tidak direvisi/diubah</small>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>