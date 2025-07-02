<?php

namespace App\Exports;

use App\Models\VerifikasiWisuda;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerifWisudaExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    use Exportable;

    public $tahun;
    public $type;
    public $autoDelete;
    private $rowNumber = 0;
    public $awal = 3;
    private $dataToDelete = []; // Simpan ID data yang akan dihapus

    public function __construct($tahun, $type = 'verifikasi', $autoDelete = false)
    {
        $this->tahun = $tahun;
        $this->type = $type;
        $this->autoDelete = $autoDelete;
    }

    public function headings(): array
    {
        $title = $this->type === 'wisudawan'
            ? 'Rekap Data Wisudawan Tahun ' . $this->tahun
            : 'Rekap Data Verifikasi Wisuda Tahun ' . $this->tahun;

        return [
            [$title],
            [],
            [
                'NO',
                'NIM',
                'Nama',
                'No Seri Ijazah',
                'Periode Wisuda',
                'Catatan'
            ]
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = VerifikasiWisuda::with('user.prodis', 'status')
            ->whereYear('created_at', $this->tahun)
            ->where('status_id', 2); // Ambil hanya status_id = 2

        $data = $query->get();

        // Simpan ID untuk dihapus nanti jika autoDelete aktif
        if ($this->autoDelete) {
            $this->dataToDelete = $data->pluck('id')->toArray();
        }

        return $data;
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $this->awal++;
        return [
            $this->rowNumber,
            $row->user->nim,
            $row->user->name,
            $row->no_seri_ijazah,
            ($row->periode_wisuda) ? Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y') : '',
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge hanya sesuai jumlah kolom aktif (A–F)
        $sheet->mergeCells('A1:F2');

        // Set lebar kolom A sampai F
        $sheet->getColumnDimension('A')->setWidth(6);   // NO
        $sheet->getColumnDimension('B')->setWidth(20);  // NIM
        $sheet->getColumnDimension('C')->setWidth(30);  // Nama
        $sheet->getColumnDimension('D')->setWidth(20);  // No Seri Ijazah
        $sheet->getColumnDimension('E')->setWidth(25);  // Periode Wisuda
        $sheet->getColumnDimension('F')->setWidth(35);  // Catatan

        // Posisi teks
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:F2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Header tabel baris ke-3
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A1:F3')->getFont()->setName('Times New Roman')->setSize(12);
        $sheet->getStyle('A3:F3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:F3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('6699CC');

        // Border semua cell dari A3 sampai baris terakhir (A to F)
        $sheet->getStyle('A3:F' . $this->awal)
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Hapus data setelah styling selesai (artinya export sudah selesai)
        if ($this->autoDelete && !empty($this->dataToDelete)) {
            $this->deleteExportedData();
        }
    }

    /**
     * Hapus data yang sudah diexport
     */
    private function deleteExportedData()
    {
        try {
            DB::transaction(function () {
                VerifikasiWisuda::whereIn('id', $this->dataToDelete)->delete();
            });
        } catch (\Exception $e) {
            // Log error jika diperlukan
            \Log::error('Gagal menghapus data setelah export: ' . $e->getMessage());
        }
    }
}
