<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Ajuan Verifikasi Wisuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        <tr>
                            <td>
                                Nama Mahasiswa
                            </td>
                            <td id="detail-nama"></td>
                        </tr>
                        <tr>
                            <td>
                                NIM Mahasiswa
                            </td>
                            <td id="detail-nim"></td>
                        </tr>
                        <tr>
                            <td>
                                Prodi Mahasiswa
                            </td>
                            <td id="detail-prodi"></td>
                        </tr>
                        <tr>
                            <td>
                                File Syarat
                            </td>
                            <td>
                                : <a href="" target="_blank" class="btn btn-primary btn-small" id="detail-file"><i class="fa fa-file"></i>Lihat File</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No Seri Ijazah
                            </td>
                            <td id="detail-ijazah"></td>
                        </tr>
                        <tr>
                            <td>
                                Periode Wisuda
                            </td>
                            <td id="detail-periode"></td>
                        </tr>
                        <tr>
                            <td>
                                Status Ajuan
                            </td>
                            <td>
                                : <button id="detail-status" disabled></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Catatan
                            </td>
                            <td id="detail-catatan">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Kode Akses
                            </td>
                            <td id="detail-kode">
                                
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                @can('staff')
                <button class="btn btn-success btn-proses" id="tombol-proses" data-id="" hidden><i class="fa fa-gear"></i> Proses</button>
                @endcan
            </div>
        </div>
    </div>
</div>