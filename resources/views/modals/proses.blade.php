<div class="modal fade" id="modalProses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Proses Ajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-proses" enctype="multipart/form-data">
                <div class="modal-body text-dark">
                    <div class="form-group">
                        <label class="form-label" for="status_id">Status Ajuan <span class="text-danger">*</span></label>
                        <select class="form-select" data-trigger name="status_id" id="status_id">
                            @foreach ($status as $item)
                                @if ($item->gate == auth()->user()->role)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="form-no-surat" hidden>
                        <label class="form-label" for="no_surat">Nomor Surat: <span class="text-danger">*</span></label>
                        <input class="form-control" id="no_surat" rows="2" name="no_surat">
                    </div>
                    <div class="form-group" id="form-surat-hasil" hidden>
                        <label for="customFile1" class="form-label custom-file-input">
                            Surat Hasil <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" type="file" id="customFile1" name="file" accept="application/pdf">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="catatan-proses">Catatan:</label>
                        <textarea class="form-control" id="catatan-proses" rows="2" name="catatan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>