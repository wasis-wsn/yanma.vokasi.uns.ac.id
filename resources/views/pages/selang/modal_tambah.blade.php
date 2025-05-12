<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Selang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('selang.store')}}"
                method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <p>
                        Scan dokumen berikut dan jadikan satu file .pdf: 
                        <ol>
                            <li>
                                Scan Surat Permohonan Ijin Selang (dapat di download pada SIAKAD setelah ajuan Selang di SIAKAD)
                            </li>
                            <li>
                                Scan KHS terakhir
                            </li>
                            <li>
                                Scan rekap pembayaran (Kuitansi download dari SIAKAD)
                            </li>
                            <li>
                                Scan ijin selang atau perpanjangan masa studi sebelumnya (Bila pernah *)
                            </li>
                        </ol>
                    </p>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">Unggah File PDF disini <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
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
                        <label class="form-label" for="tahun_akademik">Tahun Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="tahun_akademik" @disabled(auth()->user()->role == '1')>
                            @foreach ($tahunAkademik as $tahun)
                                <option value="{{$tahun->id}}" @selected($selang->tahun_akademik_id == $tahun->id)>
                                    {{$tahun->tahun_akademik}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tahun_akademik_id" value="{{$selang->tahun_akademik_id}}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester">Semester Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="semester" @disabled(auth()->user()->role == '1')>
                            @foreach ($semester as $s)
                                <option value="{{$s->id}}" @selected($selang->semester_id == $s->id)>
                                    {{$s->semester}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="semester_id" value="{{$selang->semester_id}}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="alasan">Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan" rows="2" name="alasan"></textarea>
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