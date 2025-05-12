<?php

namespace App\Exports;

use App\Models\Legalisir;
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

class LegalisirExport implements FromCollection, WithHeadings, WithStyles, WithMapping
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
            ['Rekap Data Legalisir Tahun ' . $this->tahun],
            [],
            [
                'NO',
                'Tanggal Submit',
                'NIM',
                'Nama',
                'No WhatsApp',
                'Prodi',
                'Tanggal Ambil',
                'Legalisir',
                'Jumlah',
                'Keperluan',
                'Tahun Lulus',
                'Status',
                'Catatan',
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Legalisir::with('prodi', 'status')
        ->whereYear('created_at', $this->tahun)
        ->get();
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $this->awal++;
        return [
            $this->rowNumber,
            Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s'),
            $row->nim,
            $row->name,
            $row->no_wa,
            $row->prodi->name,
            ($row->tanggal_ambil) ? Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') : '',
            $row->legalisir,
            $row->jumlah,
            $row->keperluan,
            $row->tahun_lulus,
            $row->status->name,
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:M2');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(13);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:M2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:M3')->getFont()->setBold(true);
        $sheet->getStyle('A1:M3')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:M3')->getFont()->setSize(12);
        $sheet->getStyle('A3:M3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:M3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6699CC');
        $sheet->getStyle('A3:M' . $this->awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}
