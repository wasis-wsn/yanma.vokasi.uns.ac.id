<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Ajuan Legalisir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        <tr>
                            <td>
                                Nama Alumni
                            </td>
                            <td id="detail-nama"></td>
                        </tr>
                        <tr>
                            <td>
                                NIM Alumni
                            </td>
                            <td id="detail-nim"></td>
                        </tr>
                        <tr>
                            <td>
                                Prodi Alumni
                            </td>
                            <td id="detail-prodi"></td>
                        </tr>
                        <tr>
                            <td>
                                Tahun Lulus
                            </td>
                            <td id="detail-tahun_lulus"></td>
                        </tr>
                        <tr>
                            <td>
                                Nomor WhatsApp Alumni
                            </td>
                            <td id="detail-no_wa"></td>
                        </tr>
                        <tr>
                            <td>
                                Legalisir
                            </td>
                            <td id="detail-legalisir"></td>
                        </tr>
                        <tr>
                            <td>
                                Jumlah Legalisir
                            </td>
                            <td id="detail-jumlah"></td>
                        </tr>
                        <tr>
                            <td>
                                Keperluan Legalisir
                            </td>
                            <td id="detail-keperluan"></td>
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
                <button class="btn btn-success btn-proses" id="tombol-proses" hidden><i class="fa fa-file-pen"></i> Proses</button>
                @endcan
            </div>
        </div>
    </div>
</div>