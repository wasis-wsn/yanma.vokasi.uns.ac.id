<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ajuan Undur Diri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('undurDiri.store') }}"
                method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <p>
                        Scan dokumen berikut dan jadikan satu file .pdf: 
                        <ol>
                            <li>
                                Surat Pernyataan mahasiswa (Download dari Siakad)
                            </li>
                            <li>
                                Kuitansi Pembayaran SPP & UKT Terakhir (Download dari Siakad)
                            </li>
                            <li>
                                Transkrip nilai
                            </li>
                            <li>
                                Surat Keterangan Bebas Pinjaman buku dari UPT Perpustakaan
                            </li>
                            <li>
                                Surat Keterangan Bebas KOPMA UNS
                            </li>
                            <li>
                                Surat Keterangan Bebas Laboratorium (*Jika ada)
                            </li>
                        </ol>
                    </p>
                    <div class="form-group">
                        <label for="customFile1" class="form-label custom-file-input">Unggah File PDF disini <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                        <small class="text-danger">Ukuran Maks File 10 MB</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tahun_akademik">Tahun Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="tahun_akademik" @disabled(auth()->user()->role == '1')>
                            @foreach ($tahunAkademik as $tahun)
                                <option value="{{$tahun->id}}" @selected($layanan->tahun_akademik_id == $tahun->id)>
                                    {{$tahun->tahun_akademik}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tahun_akademik_id" value="{{$layanan->tahun_akademik_id}}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="semester">Semester Akademik<span class="text-danger">*</span>:</label>
                        <select class="form-select" data-trigger id="semester" @disabled(auth()->user()->role == '1')>
                            @foreach ($semester as $s)
                                <option value="{{$s->id}}" @selected($layanan->semester_id == $s->id)>
                                    {{$s->semester}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="semester_id" value="{{$layanan->semester_id}}">
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