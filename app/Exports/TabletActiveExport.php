<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class TabletActiveExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithDrawings, WithCustomStartCell
{
    protected $periode;
    protected $lokasi;
    protected $departemen;
    protected $status;

    public function __construct($periode = null, $lokasi = null, $departemen = null, $status = null)
    {
        $this->periode = $periode;
        $this->lokasi = $lokasi;
        $this->departemen = $departemen;
        $this->status = $status;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $query = Barang::with(['menuAktif.lokasi', 'menuAktif.departemen', 'menuAktif.ipAddress'])
            ->join('tbl_menu_aktif', 'tbl_barang.id_barang', '=', 'tbl_menu_aktif.id_barang')
            ->join('tbl_lokasi', 'tbl_menu_aktif.id_lokasi', '=', 'tbl_lokasi.id_lokasi')
            ->join('tbl_departemen', 'tbl_menu_aktif.id_departemen', '=', 'tbl_departemen.id_departemen')
            ->whereHas('menuAktif', function ($q) {
            $q->where('jenis_barang', 'Tablet');
            });

        if (!empty($this->periode)) {
            $carbonPeriode = Carbon::createFromFormat('Y-m', $this->periode);

            $query->whereHas('riwayat', function ($q) use ($carbonPeriode) {
            $q->whereYear('waktu_awal', '<=', $carbonPeriode->year)
            ->whereMonth('waktu_awal', '<=', $carbonPeriode->month)
            ->where(function ($query) use ($carbonPeriode) {
                $query->whereNull('waktu_akhir')  // Jika masih aktif (tidak ada waktu akhir)
                    ->orWhereYear('waktu_akhir', '>=', $carbonPeriode->year)
                    ->orWhereMonth('waktu_akhir', '>=', $carbonPeriode->month);
            });
            });
        }

        if (!empty($this->lokasi)) {
            $query->whereHas('menuAktif', function ($q) {
            $q->where('id_lokasi', $this->lokasi);
            });
        }

        if (!empty($this->departemen)) {
            $query->whereHas('menuAktif', function ($q) {
            $q->where('id_departemen', $this->departemen);
            });
        }

        $tablets = $query->orderBy('tbl_lokasi.nama_lokasi', 'asc')
                  ->orderBy('tbl_departemen.nama_departemen', 'asc')
                  ->get();

        if ($tablets->isEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'Tidak ada data yang ditemukan dengan filter yang diberikan.'
            ]);
        }

        return $tablets;
    }



    public function headings(): array
    {
        return [
            ['NO', 'LOKASI', 'DEPARTEMEN', 'IP ADDRESS', 'USER', 'MODEL', 'TYPE/MERK', 'SERIAL', 'TAHUN PEROLEHAN', 'STATUS', 'KETERANGAN']
        ];
    }

    public function map($tablet): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            strtoupper($tablet->menuAktif->first()->lokasi->nama_lokasi ?? '-'),
            strtoupper($tablet->menuAktif->first()->departemen->nama_departemen ?? '-'),
            strtoupper($tablet->menuAktif->first()->ipAddress->ip_address ?? '-'),
            strtoupper($tablet->menuAktif->first()->user ?? '-'),
            strtoupper($tablet->model),
            strtoupper($tablet->tipe_merk ?? '-'),
            strtoupper($tablet->serial ?? '-'),
            strtoupper(Carbon::parse($tablet->tahun_perolehan)->format('M Y')),
            strtoupper($tablet->status === 'Aktif' ? 'DIPAKAI' : ($tablet->menuAktif->first()->status ?? '-')),
            strtoupper($tablet->menuAktif->first()->keterangan ?? '-')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add title
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $sheet->mergeCells('A4:I4');
        $sheet->setCellValue('A2', 'MIS - PT. RAJAWALI HIYOTO');
        $sheet->setCellValue('A3', 'INVENTARISASI DAN KELAYAKAN TABLET');
        $sheet->setCellValue('A4', '( EBF.002.02.G )');
        
        // Add Periode and Hal with borders
        $sheet->mergeCells('J2:K2');
        $sheet->mergeCells('J3:K3');
        $sheet->setCellValue('J2', 'PERIODE: ' . (empty($this->periode) ? 'Semua Periode' : strtoupper(Carbon::parse($this->periode)->format('M Y'))));
        $sheet->setCellValue('J3', 'HAL: 1/1');

        // Add borders to header section
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ];

        // Apply borders to header sections
        $sheet->getStyle('A2:K4')->applyFromArray($borderStyle);

        // Style the table headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        // Apply styles
        $sheet->getStyle('A6:K6')->applyFromArray($headerStyle);
        $sheet->getStyle('A2:K4')->getFont()->setBold(true);
        $sheet->getStyle('A2:K4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set column alignment
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Apply borders to all data cells
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:K' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}
