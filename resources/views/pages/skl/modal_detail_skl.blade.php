<div class="modal fade" id="modalDetailSKL" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Ajuan SKL</h5>
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
                                Lembar Persetujuan Revisi Tugas Akhir
                            </td>
                            <td>
                                : <a href="" class="btn btn-primary btn-sm" target="_blank" id="detail-lembar-persetujuan"><i class="fa fa-file"></i> Lihat File</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Screenshot Bukti Ajuan SKL di SIAKAD
                            </td>
                            <td>
                                : <a href="" class="btn btn-primary btn-sm" target="_blank" id="detail-ss-bukti"><i class="fa fa-file"></i> Lihat File</a>
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
                                Status Ajuan
                            </td>
                            <td>
                                : <button id="detail-status" disabled></button>
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