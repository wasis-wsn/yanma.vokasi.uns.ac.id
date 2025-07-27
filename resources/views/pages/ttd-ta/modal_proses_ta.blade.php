<div class="modal fade" id="modalProsesTA" tabindex="-1" aria-labelledby="titleModalProses" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalProsesTA">Proses Ajuan TTD TA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-proses-ta">
                <input type="hidden" name="status" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="status_ta_id">Status Ajuan <span class="text-danger">*</span></label>
                        <select class="form-select" data-trigger name="status_id" id="status_ta_id">
                            @foreach ($status_ta as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="catatan-proses-ta">Catatan: (Opsional)</label>
                        <textarea class="form-control" id="catatan-proses-ta" rows="2" name="catatan"></textarea>
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