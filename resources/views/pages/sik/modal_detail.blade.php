<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Ajuan SKMK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        <tr>
                            <td>
                                Nama Pembina Ormawa
                            </td>
                            <td id="detail-pembina"></td>
                        </tr>
                        <tr>
                            <td>
                                Ormawa
                            </td>
                            <td id="detail-ormawa"></td>
                        </tr>
                        <tr>
                            <td>
                                Nama Kegiatan
                            </td>
                            <td id="detail-kegiatan"></td>
                        </tr>
                        <tr>
                            <td>
                                Ketua Kegiatan
                            </td>
                            <td id="detail-ketua"></td>
                        </tr>
                        <tr>
                            <td>
                                Nomor WhatsApp Ketua Kegiatan
                            </td>
                            <td id="detail-no-ketua"></td>
                        </tr>
                        <tr>
                            <td>
                                Nomor Surat Ormawa
                            </td>
                            <td id="detail-no-ormawa"></td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal Surat Ormawa
                            </td>
                            <td id="detail-tanggal-ormawa"></td>
                        </tr>
                        <tr>
                            <td>
                                Apakah Mengajukan Dana ke SV UNS
                            </td>
                            <td id="detail-is-dana"></td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal Kegiatan
                            </td>
                            <td id="detail-tanggal-kegiatan"></td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal Maksimal Penyerahan LPJ dan SPJ
                            </td>
                            <td id="detail-tanggal-lpj"></td>
                        </tr>
                        <tr>
                            <td>
                                Tempat Kegiatan
                            </td>
                            <td id="detail-tempat-kegiatan"></td>
                        </tr>
                        <tr>
                            <td>
                                File Upload
                            </td>
                            <td>
                                : <a href="" target="_blank" class="btn btn-primary btn-small" id="detail-file"><i class="fa fa-file"></i> Lihat File</a>
                            </td>
                        </tr>
                        @can('staff')
                        <tr>
                            <td>
                                Nomor Surat
                            </td>
                            <td id="detail-no">
                                
                            </td>
                        </tr>
                        @endcan
                        <tr>
                            <td>
                                Catatan
                            </td>
                            <td id="detail-catatan">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal Diproses
                            </td>
                            <td id="detail-proses">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Status Ajuan
                            </td>
                            <td>
                                : <button id="detail-status"></button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                @can('staff')
                <button class="btn btn-success btn-proses" id="tombol-proses" data-id="" hidden><i class="fa fa-file-pen"></i> Buat Surat</button>
                @endcan
            </div>
        </div>
    </div>
</div>