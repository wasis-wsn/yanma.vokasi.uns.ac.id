<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Ajuan Surat Keterangan / Pengantar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-edit" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="catatan-revisi">Catatan Revisi:</label>
                        <textarea class="form-control" id="catatan-revisi" rows="2" disabled></textarea>
                    </div>
                    {{-- <div class="form-group">
                        <label class="form-label" for="tahun_akademik-revisi">Tahun Akademik <span class="text-danger">*</span>:</label>
                        <input class="form-control" id="tahun_akademik-revisi" type="text" name="tahun_akademik" placeholder="Tahun Akademik - Semester">
                        <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan. Contoh <i><b>2023/2024 - Gasal</b></i></small>
                    </div> --}}
                    <div class="form-group">
                        <label class="form-label" for="tahun_akademik-revisi">Tahun Akademik <span class="text-danger">*</span>:</label>
                        <select name="tahun_akademik_id" id="tahun_akademik-revisi"class="selectpicker form-control" data-style="py-0">
                            @foreach ($tahunAkademik as $tahun)
                                <option value="{{$tahun->id}}">{{$tahun->tahun_akademik}}</option>
                            @endforeach
                        </select>
                        <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester-revisi">Semester <span class="text-danger">*</span>:</label>
                        <select name="semester_id" id="semester-revisi"class="selectpicker form-control" data-style="py-0">
                            @foreach ($semester as $s)
                                <option value="{{$s->id}}">{{$s->semester}}</option>
                            @endforeach
                        </select>
                        <small id="tahunHelp" class="form-text text-muted">Sesuai dengan KRS yang dilampirkan.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="keperluan-revisi">Keperluan Surat: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keperluan-revisi" rows="2" name="keperluan"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="customFile1-revisi" class="form-label custom-file-input">Upload File</label>
                        <input class="form-control" type="file" id="customFile1-revisi" name="file" accept="application/pdf">
                        <small class="text-danger"><i class="fa fa-warning"></i> Jangan Upload Apapun Jika File Tidak Direvisi atau Diedit!</small>
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