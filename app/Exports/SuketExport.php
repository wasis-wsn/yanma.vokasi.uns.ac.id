<?php

namespace App\Exports;

use App\Models\SuratKeterangan;
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

class SuketExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    use Exportable;

    public $tahun;
    public $semester;
    private $rowNumber = 0;
    public $awal = 3;

    public function __construct($tahun, $semester)
    {
        $this->tahun = $tahun;
        $this->semester = $semester;
    }

    public function headings(): array
    {
        return [
            ['Rekap Data Surat Keterangan Tahun Akademik ' . $this->tahun->tahun_akademik . ' - ' . $this->semester->semester],
            [],
            [
                'NO',
                'Tanggal Submit',
                'Nomor Surat',
                'NIM',
                'Nama',
                'Prodi',
                'Keperluan',
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
        // return SuratKeterangan::with('user.prodis', 'status')->whereYear('created_at', $this->tahun)->get();
        return SuratKeterangan::with('user.prodis', 'status')
                ->where('tahun_akademik_id', $this->tahun->id)
                ->where('semester_id', $this->semester->id)
                ->get();
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $this->awal++;
        return [
            $this->rowNumber,
            Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s'),
            $row->no_surat,
            $row->user->nim,
            $row->user->name,
            $row->user->prodis->name,
            $row->keperluan,
            $row->status->name,
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:I2');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:I2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:I3')->getFont()->setBold(true);
        $sheet->getStyle('A1:I3')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:I3')->getFont()->setSize(12);
        $sheet->getStyle('A3:I3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:I3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6699CC');
        $sheet->getStyle('A3:I' . $this->awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}
