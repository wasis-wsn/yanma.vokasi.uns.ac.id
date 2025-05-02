<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Pembayaran UKT Diluar Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('diluarJadwal.store') }}"
                method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <p>
                        Scan dokumen berikut dan jadikan satu file .pdf: 
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
                        <label for="customFile1" class="form-label custom-file-input">Unggah File PDF disini <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="surat_permohonan" accept="application/pdf">
                        <span class="text-danger">Ukuran Maks File 10 MB</span>
                    </div>
                    @can('staff')
                        <div class="form-group" id="div_mahasiswa">
                            <label class="form-label" for="mahasiswa">Nama Mahasiswa <span class="text-danger">*</span>:</label>
                            <select name="mahasiswa" id="mahasiswa" class="form-control">
                            </select>
                        </div>
                    @endcan
                    <div class="form-group">
                        <label class="form-label" for="semester_romawi">Semester <span class="text-danger">*</span>:</label>
                        <select name="semester_romawi" id="semester_romawi" class="selectpicker form-control" data-style="py-0">
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
                        <label class="form-label" for="tahun_akademik">Tahun Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="tahun_akademik" name="tahun_akademik_id" @disabled(auth()->user()->role == '1')>
                            @foreach ($tahunAkademik as $tahun)
                                <option value="{{$tahun->id}}" @selected($diluarJadwal->tahun_akademik_id == $tahun->id)>
                                    {{$tahun->tahun_akademik}}
                                </option>
                            @endforeach
                        </select>
                        {{-- <input type="hidden" name="tahun_akademik_id" value="{{$diluarJadwal->tahun_akademik_id}}"> --}}
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester">Semester Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="semester" name="semester_id" @disabled(auth()->user()->role == '1')>
                            @foreach ($semester as $s)
                                <option value="{{$s->id}}" @selected($diluarJadwal->semester_id == $s->id)>
                                    {{$s->semester}}
                                </option>
                            @endforeach
                        </select>
                        {{-- <input type="hidden" name="semester_id" value="{{$diluarJadwal->semester_id}}"> --}}
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="alasan">Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan" rows="2" name="alasan"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tanggal_bayar">Tanggal Akan Membayar <span class="text-danger">*</span></label>
                        <input class="form-control" id="tanggal_bayar" type="date" name="tanggal_bayar">
                        <small>
                            Mohon diperhatikan bahwa setelah ajuan disetujui universitas, akses membayar biasanya akan dibuka selama 1x24 jam. 
                            Harap isikan tanggal pada hari kerja yaitu Senin-Jumat selain tanggal merah.
                        </small>
                    </div>
                    {{-- <div class="form-group">
                        <label for="bukti_bayar_ukt" class="form-label custom-file-input">Bukti Pembayaran UKT Terakhir <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="bukti_bayar_ukt" name="bukti_bayar_ukt" accept="application/pdf">
                        <small>
                            Mohon utamakan mengunggah scan bukti bayar asli dari bank, dan pastikan hasil scan JELAS. 
                            Apabila slip asli dari bank hilang, mahasiswa bisa mengunduh kuitansi dari Siakad.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="izin_cuti" class="form-label custom-file-input">Izin Cuti/Selang (bila ada)</label>
                        <input class="form-control" type="file" id="izin_cuti" name="izin_cuti" accept="application/pdf">
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