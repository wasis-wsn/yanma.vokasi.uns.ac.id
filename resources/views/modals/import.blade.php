{{-- Import Modal --}}
<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportLabel">Import Data Konfirmasi Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-import" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tahun_import" class="form-label">Tahun</label>
                        <select class="form-select" name="tahun" id="tahun_import" required>
                            @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->tahun }}" {{ $tahun->tahun == date('Y') ? 'selected' : '' }}>
                                {{ $tahun->tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file_import" class="form-label">File Excel/CSV</label>
                        <input type="file" class="form-control" name="file" id="file_import"
                               accept=".xlsx,.xls,.csv,.txt,text/csv,application/csv,text/comma-separated-values" required>
                        <div class="form-text">Format yang didukung: Excel (.xlsx, .xls) atau CSV (.csv). Maksimal 10MB.</div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Catatan:</strong>
                        <p>Import sesuai dengan template yang sudah diberikan</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-info">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
