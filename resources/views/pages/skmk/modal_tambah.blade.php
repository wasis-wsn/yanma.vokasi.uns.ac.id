<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan SKMK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('skmk.store')}}" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <p>
                                Siapkan file .pdf: 
                                <ol>
                                    <li>
                                        Scan Asli Kartu Rencana Studi (KRS) Semester Terakhir
                                    </li>
                                    <li>
                                        Scan Asli Surat Keterangan Gaji Orangtua/wali
                                    </li>
                                </ol>
                                berkas dijadikan <b>satu file</b> dalam bentuk File <b>PDF</b>
                            </p>
                            <div class="form-group">
                                <label for="customFile1" class="form-label custom-file-input">Unggah File PDF disini <span class="text-danger">*</span></label>
                                <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="py-2">Data Mahasiswa</h4>
                            <div class="form-group">
                                <label class="form-label" for="semester">Semester<span class="text-danger">*</span>:</label>
                                <select name="semester_romawi" id="semester"class="selectpicker form-control" data-style="py-0">
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
                                <label class="form-label" for="tahun_akademik">Tahun Akademik <span class="text-danger">*</span>:</label>
                                <select name="tahun_akademik_id" id="tahun_akademik"class="selectpicker form-control" data-style="py-0">
                                <option>Pilih Tahun Akademik</option>
                                    @foreach ($tahunAkademik as $tahun)
                                        <option value="{{$tahun->id}}">{{$tahun->tahun_akademik}}</option>
                                    @endforeach
                                </select>
                                <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan.</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="semester_akademik">Semester Akademik<span class="text-danger">*</span>:</label>
                                <select name="semester_id" id="semester_akademik"class="selectpicker form-control" data-style="py-0">
                                <option>Pilih Semester Akademik</option>
                                    @foreach ($semester as $s)
                                        <option value="{{$s->id}}">{{$s->semester}}</option>
                                    @endforeach
                                </select>
                                <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="py-2">Data Orang Tua / Wali</h4>
                            <div class="form-group">
                                <label class="form-label" for="nama_ortu">Nama Bapak / Ibu <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="nama_ortu" type="text" name="nama_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="nip_ortu">NIP / NRP (isi tanpa spasi) <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="nip_ortu" type="text" name="nip_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="pangkat_ortu">Pangkat / Golongan <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="pangkat_ortu" type="text" name="pangkat_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="instansi_ortu">Nama Instansi Bekerja <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="instansi_ortu" type="text" name="instansi_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="alamat_instansi">Alamat Instansi <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="alamat_instansi" type="text" name="alamat_instansi">
                            </div>
                        </div>
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