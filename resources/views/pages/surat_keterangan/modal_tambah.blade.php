<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Surat Keterangan / Pengantar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('suket.store')}}" method="POST" id="form-tambah" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-dark">
                    <p>
                        Siapkan file .pdf: 
                        <ol>
                            <li>
                                Scan Asli Kartu Rencana Studi (KRS) Semester Terakhir
                            </li>
                        </ol>
                    </p>
                    {{-- <div class="form-group">
                        <label class="form-label" for="tahun_akademik">Tahun Akademik <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="tahun_akademik" type="text" name="tahun_akademik" placeholder="Tahun Akademik - Semester">
                        <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan. Contoh <i><b>2023/2024 - Gasal</b></i></small>
                    </div> --}}
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
                        <label class="form-label" for="semester">Semester <span class="text-danger">*</span>:</label>
                        <select name="semester_id" id="semester"class="selectpicker form-control" data-style="py-0">
                            <option>Pilih Semester</option>
                            @foreach ($semester as $s)
                                <option value="{{$s->id}}">{{$s->semester}}</option>
                            @endforeach
                        </select>
                        <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="keperluan">Keperluan Surat: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keperluan" rows="2" name="keperluan"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">Unggah File Hasil Scan KRS <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
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