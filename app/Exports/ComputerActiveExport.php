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

class ComputerActiveExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithDrawings, WithCustomStartCell
{
    protected $periode;
    protected $lokasi;
    protected $departemen;
    protected $status;
    protected $kelayakan;

    public function __construct($periode = null, $lokasi = null, $departemen = null, $status = null, $kelayakan = null)
    {
        $this->periode = $periode;
        $this->lokasi = $lokasi;
        $this->departemen = $departemen;
        $this->status = $status;
        $this->kelayakan = $kelayakan;
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
            ->whereHas('menuAktif', function ($q) {
                $q->where('jenis_barang', 'Komputer');
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

        $computers = $query->get();

        if ($computers->isEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'Tidak ada data yang ditemukan dengan filter yang diberikan.'
            ]);
        }

        return $computers;
    }



    public function headings(): array
    {
        return [
            ['NO', 'LOKASI', 'DEPARTEMEN', 'KOMPUTER NAME', 'IP ADDRESS', 'OPERATING SYSTEM', 'USER', 'MODEL', 'TYPE/MERK', 'SERIAL', '', 'TAHUN PEROLEHAN', 'KELAYAKAN', 'STATUS', 'KETERANGAN'],
            ['', '', '', '', '', '', '', '', '', 'CPU', 'MONITOR', '', '', '', '']
        ];
    }

    public function map($computer): array
    {
        static $row = 0;
        $row++;

        $serial = json_decode($computer->serial);

        return [
            $row,
            strtoupper($computer->menuAktif->first()->lokasi->nama_lokasi ?? '-'),
            strtoupper($computer->menuAktif->first()->departemen->nama_departemen ?? '-'),
            strtoupper($computer->menuAktif->first()->komputer_name ?? '-'),
            strtoupper($computer->menuAktif->first()->ipAddress->ip_address ?? '-'),
            strtoupper($computer->operating_system ?? '-'),
            strtoupper($computer->menuAktif->first()->user ?? '-'),
            strtoupper($computer->model),
            strtoupper($computer->tipe_merk ?? '-'),
            strtoupper($serial ? $serial->cpu : ($computer->serial ?? '-')),
            strtoupper($serial ? $serial->monitor : '-'),
            strtoupper(Carbon::parse($computer->tahun_perolehan)->format('M Y')),
            strtoupper($computer->kelayakan . '%'),
            strtoupper($computer->status === 'Aktif' ? 'DIPAKAI' : ($computer->menuAktif->first()->status ?? '-')),
            strtoupper($computer->menuAktif->first()->keterangan ?? '-')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add title
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('A3:M3');
        $sheet->mergeCells('A4:M4');
        $sheet->setCellValue('A2', 'MIS - PT. RAJAWALI HIYOTO');
        $sheet->setCellValue('A3', 'INVENTARISASI DAN KELAYAKAN KOMPUTER');
        $sheet->setCellValue('A4', '( EBF.002.02.G )');
        
        // Add Periode and Hal with borders
        $sheet->mergeCells('N2:O2');
        $sheet->mergeCells('N3:O3');
        $sheet->setCellValue('N2', 'PERIODE: ' . (empty($this->periode) ? 'Semua Periode' : strtoupper(Carbon::parse($this->periode)->format('M Y'))));
        $sheet->setCellValue('N3', 'HAL: 1/1');

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
        $sheet->getStyle('A2:O4')->applyFromArray($borderStyle);

        // Merge SERIAL columns
        $sheet->mergeCells('J6:K6');

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
        $sheet->getStyle('A6:O7')->applyFromArray($headerStyle);
        $sheet->getStyle('A2:O4')->getFont()->setBold(true);
        $sheet->getStyle('A2:O4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set column alignment
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M:M')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Apply borders to all data cells
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:O' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}