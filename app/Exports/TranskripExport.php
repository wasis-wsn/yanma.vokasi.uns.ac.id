<?php

namespace App\Exports;

use App\Models\TranskripNilai;
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

class TranskripExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    use Exportable;

    public $tahun;
    private $rowNumber = 0;
    public $awal = 3;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function headings(): array
    {
        return [
            ['Rekap Data Transkrip Nilai Tahun ' . $this->tahun],
            [],
            [
                'NO',
                'Tanggal Submit',
                'Nomor Transkrip',
                'NIM',
                'Nama',
                'Prodi',
                'Periode Wisuda',
                'Status',
                'Diambil Tanggal',
                'Catatan'
            ]
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return TranskripNilai::with(['user' => function ($query) {
            $query->orderBy('name'); // Urutkan berdasarkan nama pengguna
        }, 'user.prodis'])->whereYear('created_at', $this->tahun)->get();
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $this->awal++;
        return [
            $this->rowNumber,
            Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s'),
            $row->no_transkrip,
            $row->user->nim,
            $row->user->name,
            $row->user->prodis->name,
            ($row->periode_wisuda) ? Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y') : '',
            $row->status->name,
            ($row->tanggal_ambil) ? Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y H:i:s') : '',
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:J2');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:J2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:J3')->getFont()->setBold(true);
        $sheet->getStyle('A1:J3')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:J3')->getFont()->setSize(12);
        $sheet->getStyle('A3:J3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:J3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6699CC');
        $sheet->getStyle('A3:J' . $this->awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}
