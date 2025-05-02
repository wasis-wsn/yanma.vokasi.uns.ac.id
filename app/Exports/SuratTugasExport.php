<?php

namespace App\Exports;

use App\Models\SuratTugas;
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
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SuratTugasExport extends DefaultValueBinder implements WithCustomValueBinder, FromCollection, WithHeadings, WithStyles, WithMapping
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
            ['Rekap Data Surat Tugas Delegasi Tahun ' . $this->tahun],
            [],
            [
                'NO',
                'Tanggal Submit',
                'Nomor Surat',
                'Nama Kegiatan',
                'NIM Ketua',
                'Nama Ketua',
                'Prodi Ketua',
                'Mulai Kegiatan',
                'Selesai Kegiatan',
                'Penyelenggara Kegiatan',
                'Tempat Kegiatan',
                'Delegasi',
                'Jumlah Peserta',
                'Dosen Pembimbing',
                'NIP Dosen Pembimbing',
                'NIDN Dosen Pembimbing',
                'Unit Kerja Dosen Pembimbing',
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
        return SuratTugas::with('user.prodis', 'status')->whereYear('created_at', $this->tahun)->get();
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
            $row->user->nim,
            $row->user->name,
            $row->user->prodis->name,
            Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y'),
            Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y'),
            $row->penyelenggara,
            $row->tempat,
            $row->delegasi,
            $row->jumlah_peserta,
            $row->dospem,
            $row->nip_dospem,
            $row->nidn_dospem,
            $row->unit_dospem,
            $row->status->name,
            $row->catatan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:S2');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(17);
        $sheet->getColumnDimension('I')->setWidth(17);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(30);
        $sheet->getColumnDimension('O')->setWidth(25);
        $sheet->getColumnDimension('P')->setWidth(25);
        $sheet->getColumnDimension('Q')->setWidth(25);
        $sheet->getColumnDimension('R')->setWidth(16);
        $sheet->getColumnDimension('S')->setWidth(25);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:S2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:S3')->getFont()->setBold(true);
        $sheet->getStyle('A1:S3')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:S3')->getFont()->setSize(12);
        $sheet->getStyle('A3:S3')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle('A3:S3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6699CC');
        $sheet->getStyle('A3:S' . $this->awal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    public function bindValue(Cell $cell, $value)
    {
        $string_column = ['O', 'P'];
        if (in_array($cell->getColumn(), $string_column)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}
