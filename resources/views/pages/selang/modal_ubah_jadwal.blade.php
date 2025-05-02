<div class="modal fade" id="modalJadwal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ubah Jadwal Ajuan Selang/Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('selang.setting')}}" method="POST" id="form-setting">
                <div class="modal-body">
                    <div class="text-dark">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="open_datetime">Tanggal Buka:</label>
                            <div class="col-sm-9">
                                <input type="datetime-local" step="1" class="form-control" id="open_datetime" name="open_datetime" value="{{$selang->open_datetime}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="close_datetime">Tanggal Tutup:</label>
                            <div class="col-sm-9">
                                <input type="datetime-local" step="1" class="form-control" id="close_datetime" name="close_datetime" value="{{$selang->close_datetime}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="tahun_akademik_layanan">Tahun Akademik<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" data-trigger name="tahun_akademik_id" id="tahun_akademik_layanan">
                                    @foreach ($tahunAkademik as $tahun)
                                        <option value="{{$tahun->id}}" @selected($selang->tahun_akademik_id == $tahun->id)>
                                            {{$tahun->tahun_akademik}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="semester_layanan">Semester Akademik<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" data-trigger name="semester_id" id="semester_layanan">
                                    @foreach ($semester as $s)
                                        <option value="{{$s->id}}" @selected($selang->semester_id == $s->id)>
                                            {{$s->semester}}
                                        </option>
                                    @endforeach
                                </select>
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