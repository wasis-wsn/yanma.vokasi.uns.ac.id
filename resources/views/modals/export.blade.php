<div class="modal fade" id="modalExport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-export">
                @csrf
                <div class="modal-body">
                    <div class="form-group form-v9">
                        <label class="form-label" for="export_tahun_skl">Tahun <span class="text-danger">*</span></label>
                        <select class="form-select" data-trigger name="tahun" id="export_tahun_skl">
                            @foreach ($tahuns as $tahun)
                                <option value="{{$tahun->tahun}}">{{$tahun->tahun}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>