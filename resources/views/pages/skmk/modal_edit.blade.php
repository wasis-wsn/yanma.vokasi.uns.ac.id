<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditTitle">Edit Ajuan SKMK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
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
                                <label for="EditcustomFile1" class="form-label custom-file-input">Unggah File PDF</label>
                                <input class="form-control" type="file" id="EditcustomFile1" name="file" accept="application/pdf">
                                <small class="text-danger"><i class="fa fa-warning"></i> Jangan Upload Apapun Jika File Tidak Direvisi atau Diedit!</small>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="card-revisi">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label" for="catatan-revisi">Catatan Revisi:</label>
                                <textarea class="form-control" id="catatan-revisi" rows="2" disabled></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="py-2">Data Mahasiswa</h4>
                            <div class="form-group">
                                <label class="form-label" for="Editsemester">Semester <span class="text-danger">*</span>:</label>
                                <select name="semester" id="Editsemester" class="selectpicker form-control">
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
                            {{-- <div class="form-group">
                                <label class="form-label" for="Edittahun_akademik">Tahun Akademik <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="Edittahun_akademik" type="text" name="tahun_akademik" placeholder="Tahun Akademik - Semester">
                                <small id="EdittahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan. Contoh <i>2023/2024 - Gasal</i></small>
                            </div> --}}
                            <div class="form-group">
                                <label class="form-label" for="Edittahun_akademik">Tahun Akademik <span class="text-danger">*</span>:</label>
                                <select name="tahun_akademik_id" id="Edittahun_akademik"class="selectpicker form-control" data-style="py-0">
                                    @foreach ($tahunAkademik as $tahun)
                                        <option value="{{$tahun->id}}">{{$tahun->tahun_akademik}}</option>
                                    @endforeach
                                </select>
                                <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan.</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="Editsemester_akademik">Semester Akademik<span class="text-danger">*</span>:</label>
                                <select name="semester_id" id="Editsemester_akademik"class="selectpicker form-control" data-style="py-0">
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
                                <label class="form-label" for="Editnama_ortu">Nama Bapak / Ibu <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="Editnama_ortu" type="text" name="nama_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="Editnip_ortu">NIP / NRP (isi tanpa spasi) <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="Editnip_ortu" type="text" name="nip_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="Editpangkat_ortu">Pangkat / Golongan <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="Editpangkat_ortu" type="text" name="pangkat_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="Editinstansi_ortu">Nama Instansi Bekerja <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="Editinstansi_ortu" type="text" name="instansi_ortu">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="Editalamat_instansi">Alamat Instansi <span class="text-danger">*</span>:</label>
                                <input class="form-control" id="Editalamat_instansi" type="text" name="alamat_instansi">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>