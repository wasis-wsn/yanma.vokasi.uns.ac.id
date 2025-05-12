<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Perpanjangan Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('perpanjanganStudi.store')}}"
                method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
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
                        <label for="customFile1" class="form-label custom-file-input">Unggah File PDF disini <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                    </div>
                    @can('staff')
                        <div class="form-group" id="div_mahasiswa">
                            <label class="form-label" for="mahasiswa">Nama Mahasiswa <span class="text-danger">*</span>:</label>
                            <select name="mahasiswa" id="mahasiswa" class="form-control">
                            </select>
                        </div>
                    @endcan
                    <div class="form-group">
                        <label class="form-label" for="tahun_akademik">Perpanjangan Tahun Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="tahun_akademik" @disabled(auth()->user()->role == '1')>
                            @foreach ($tahunAkademik as $tahun)
                                <option value="{{$tahun->id}}" @selected($perpanjangan->tahun_akademik_id == $tahun->id)>
                                    {{$tahun->tahun_akademik}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tahun_akademik_id" value="{{$perpanjangan->tahun_akademik_id}}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester">Perpanjangan Semester Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="semester" @disabled(auth()->user()->role == '1')>
                            @foreach ($semester as $s)
                                <option value="{{$s->id}}" @selected($perpanjangan->semester_id == $s->id)>
                                    {{$s->semester}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="semester_id" value="{{$perpanjangan->semester_id}}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="perpanjangan_ke">Perpanjangan Ke <span class="text-danger">*</span>:</label>
                        <select name="perpanjangan_ke" id="perpanjangan_ke" class="selectpicker form-control" data-style="py-0">
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