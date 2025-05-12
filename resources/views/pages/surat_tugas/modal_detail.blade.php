<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Ajuan Surat Tugas Delegasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        <tr>
                            <td>
                                Nama Kegiatan
                            </td>
                            <td id="detail-kegiatan"></td>
                        </tr>
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
                                Nomor WhatsApp Mahasiswa
                            </td>
                            <td id="detail-no-wa"></td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal Kegiatan
                            </td>
                            <td id="detail-tanggal-kegiatan"></td>
                        </tr>
                        <tr>
                            <td>
                                Penyelenggara Kegiatan
                            </td>
                            <td id="detail-penyelenggara"></td>
                        </tr>
                        <tr>
                            <td>
                                Tempat Kegiatan
                            </td>
                            <td id="detail-tempat-kegiatan"></td>
                        </tr>
                        <tr>
                            <td>
                                Delegasi sebagai
                            </td>
                            <td id="detail-delegasi"></td>
                        </tr>
                        <tr>
                            <td>
                                Jumlah Peserta Delegasi
                            </td>
                            <td id="detail-peserta"></td>
                        </tr>
                        <tr>
                            <td>
                                Dosen Pembimbing
                            </td>
                            <td id="detail-dospem"></td>
                        </tr>
                        <tr>
                            <td>
                                NIP Dosen Pembimbing
                            </td>
                            <td id="detail-nip-dospem"></td>
                        </tr>
                        <tr>
                            <td>
                                NIDN Dosen Pembimbing
                            </td>
                            <td id="detail-nidn-dospem"></td>
                        </tr>
                        <tr>
                            <td>
                                Unit Kerja Dosen Pembimbing
                            </td>
                            <td id="detail-unit-dospem"></td>
                        </tr>
                        <tr>
                            <td>
                                File Upload
                            </td>
                            <td>
                                : <a href="" target="_blank" class="btn btn-primary btn-small" id="detail-file"><i class="fa fa-file"></i> Lihat File</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Nomor Surat
                            </td>
                            <td id="detail-no">
                                
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
                <button class="btn btn-success btn-proses" id="tombol-proses" data-id="" hidden><i class="fa fa-file-pen"></i> Proses</button>
                @endcan
            </div>
        </div>
    </div>
</div>