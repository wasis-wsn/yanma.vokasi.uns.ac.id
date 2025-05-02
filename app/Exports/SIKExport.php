<?php

namespace App\Exports;

use App\Models\SIK;
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

class SIKExport implements FromCollection, WithHeadings, WithStyles, WithMapping
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
            ['Rekap Data Surat Izin Kegiatan Tahun ' . $this->tahun],
            [],
            [
                'NO',
                'Tanggal Submit',
                'Nomor Surat',
                'Nama Kegiatan',
                'Ormawa',
                'Pembina',
                'NIM Ketua',
                'Nama Ketua',
                'Prodi Ketua',
                'No Surat Ormawa',
                'Tanggal Surat',
                'Tanggal Pernyataan LPJ',
                'Mulai Kegiatan',
                'Selesai Kegiatan',
                'Tempat Kegiatan',
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
        return SIK::with('ketua.prodis', 'status', 'ormawa.pembina')->whereYear('created_at', $this->tahun)->get();
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $this->awal++;
        return [
            $this->rowNumber,
            Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s'),
            $row->no_surat,
            $row->nama_kegiatan,
            $row->ormawa->name,
            $row->ormawa->pembina->name,
            $row->ketua->nim,
            $row->ketua->name,
            $row->ketua->prodis->name,
            $row->no_surat_ormawa,
            Carbon::parse($row->tanggal_surat)->translatedFormat('d F Y'),
            Carbon::parse($row->tanggal_lpj)->translatedFormat('d F Y'),
            Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y H:i:s'),
            Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y H:i:s'),
            $row->tempat,
            $row->status->name,
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:Q2');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(40);
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(30);
        $sheet->getColumnDimension('O')->setWidth(25);
        $sheet->getColumnDimension('P')->setWidth(16);
        $sheet->getColumnDimension('Q')->setWidth(25);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:Q2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Q3')->getFont()->setBold(true);
        $sheet->getStyle('A1:Q3')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:Q3')->getFont()->setSize(12);
        $sheet->getStyle('A3:Q3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:Q3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6699CC');
        $sheet->getStyle('A3:Q' . $this->awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}
