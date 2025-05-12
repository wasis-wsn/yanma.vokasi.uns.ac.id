<?php

namespace App\Exports;

use App\Models\SKMK;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SKMKExport extends DefaultValueBinder implements WithCustomValueBinder, FromCollection, WithHeadings, WithStyles, WithMapping
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
            ['Rekap Data Surat Keterangan Masih Kuliah Tahun Akademik ' . $this->tahun->tahun_akademik . ' - ' . $this->semester->semester],
            [],
            [
                'NO',
                'Tanggal Submit',
                'Nomor Surat',
                'NIM',
                'Nama',
                'Prodi',
                'Semester',
                'Tahun Akademik',
                'Nama Ortu',
                'NIP Ortu',
                'Jabatan Ortu',
                'Instansi Ortu',
                'Alamat Instansi',
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
        // return SKMK::with('user.prodis', 'status')->whereYear('created_at', $this->tahun)->get();
        return SKMK::with('user.prodis', 'status', 'semester', 'tahunAkademik')
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
            $row->semester_romawi,
            $row->tahunAkademik->tahun_akademik.' - '.$row->semester->semester,
            $row->nama_ortu,
            $row->nip_ortu,
            $row->pangkat_ortu,
            $row->instansi_ortu,
            $row->alamat_instansi,
            $row->status->name,
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:O2');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(40);
        $sheet->getColumnDimension('M')->setWidth(40);
        $sheet->getColumnDimension('N')->setWidth(16);
        $sheet->getColumnDimension('O')->setWidth(25);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:O2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:O3')->getFont()->setBold(true);
        $sheet->getStyle('A1:O3')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:O3')->getFont()->setSize(12);
        $sheet->getStyle('A3:O3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:O3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6699CC');
        $sheet->getStyle('A3:O' . $this->awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'I') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}
