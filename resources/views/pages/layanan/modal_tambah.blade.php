<div class="modal fade" id="modalTambah" role="dialog" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-tambah" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="kategori_layanan_id">Kategori Layanan <span class="text-danger">*</span>:</label>
                        <select name="kategori_layanan_id" id="kategori_layanan_id" class="selectpicker form-control" data-style="py-0">
                            @foreach ($kategoriLayanan as $kategori)
                                <option value="{{$kategori->id}}">{{$kategori->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Layanan <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="file" class="form-label custom-file-input">Template Surat Pengantar</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".doc,.docx">
                    </div>
                    <div class="form-group">
                        <label for="urutan" class="form-label">Urutan Layanan <span class="text-danger">*</span></label>
                        <input class="form-control" type="number" id="urutan" name="urutan">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="url_mhs">url Mahasiswa <span class="text-danger">*</span></label>
                        <input class="form-control" id="url_mhs" type="text" name="url_mhs">
                        <small>Contoh: https://layanan.vokasi.uns.ac.id/layanan</small>
                    </div>
                    <div class="form-group">
                        <div class="form-check d-block">
                            <input class="form-check-input" type="checkbox" value="" id="url_same">
                            <label class="form-check-label" for="url_same">
                                url Mahasiswa sama dengan url Staff
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="url_staff">url Staff <span class="text-danger">*</span></label>
                        <input class="form-control" id="url_staff" type="text" name="url_staff">
                        <small>Contoh: https://layanan.vokasi.uns.ac.id/layanan</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Yang dapat mengakses Layanan <span class="text-danger">*</span>:</label>
                        <div class="container">
                            <div class="d-flex">
                                <div class="row ml-2">
                                    @foreach ($role as $item)
                                    <div class="form-check d-block col">
                                        <input class="form-check-input" type="checkbox" value="{{$item->id}}" name="gate[]" id="{{$item->gate_name}}">
                                        <label class="form-check-label" for="{{$item->gate_name}}">
                                            {{$item->name}}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tampilkan Layanan:</label>
                        <div class="form-check d-block">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active">
                            <label class="form-check-label" for="is_active">
                                Tampilkan
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="keterangan">Keterangan Layanan:</label>
                        <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
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