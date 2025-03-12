<?php

namespace App\Exports;

use App\Models\Maintenance;
use App\Models\MenuAktif;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SwitchMaintenanceExport implements FromCollection, WithTitle, WithStyles, WithHeadings, ShouldAutoSize
{
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $lokasiId;
    protected $departemenId;
    protected $petugasList;

    public function __construct($tanggalAwal = null, $tanggalAkhir = null, $lokasiId = null, $departemenId = null, $petugasList = [])
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->lokasiId = $lokasiId;
        $this->departemenId = $departemenId;
        $this->petugasList = json_decode($petugasList, true);
    }

    public function collection()
    {
        $query = Maintenance::with(['barang', 'barang.menuAktif', 'barang.menuAktif.departemen', 'barang.menuAktif.lokasi'])
            ->whereHas('barang', function ($query) {
                $query->where('jenis_barang', 'Switch')
                    ->whereHas('menuAktif', function ($query) {
                        if ($this->departemenId) {
                            $query->where('id_departemen', $this->departemenId);
                        }
                        if ($this->lokasiId) {
                            $query->where('id_lokasi', $this->lokasiId);
                        }
                    });
            });

        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $query->whereBetween('tgl_maintenance', [$this->tanggalAwal, $this->tanggalAkhir]);
        }

        $maintenances = $query->orderBy('tgl_maintenance', 'asc')->get();
        Log::info('Found ' . $maintenances->count() . ' maintenance records');

        $result = [];
        $currentDate = null;
        $currentDay = null;

        foreach ($maintenances as $maintenance) {
            $barang = $maintenance->barang;
            if (!$barang || !$barang->menuAktif->count()) {
                continue;
            }

            $date = Carbon::parse($maintenance->tgl_maintenance);
            $dateFormatted = $date->format('d-M-y');
            
            // Check if this is a new day
            $isNewDay = $currentDate != $dateFormatted;
            
            // Only set the day name for the first record of each day
            $dayOfWeek = '';
            if ($isNewDay) {
                $dayOfWeek = strtoupper($date->locale('id')->dayName);
                $currentDay = $dayOfWeek;
                $currentDate = $dateFormatted;
            }

            foreach ($barang->menuAktif as $menuAktif) {
            $result[] = [
                'hari' => $dayOfWeek,
                'tanggal' => ($dayOfWeek != '') ? $dateFormatted : '',
                'divisi' => $menuAktif->departemen->nama_departemen ?? '',
                'node_terpakai' => (int)($maintenance->node_terpakai ?? 0),
                'node_bagus' => (int)($maintenance->node_bagus ?? 0),
                'node_rusak' => (int)($maintenance->node_rusak ?? 0) === 0 ? '0' : (int)($maintenance->node_rusak ?? 0),
                'node_kosong' => (int)(($maintenance->node_bagus ?? 0) - ($maintenance->node_terpakai ?? 0)),
                'switch' => $barang->tipe_merk ?? '',
                'status_net' => $maintenance->status_net ?? 'OK',
                'petugas' => ($isNewDay && !empty($this->petugasList)) ? implode(", ", $this->petugasList) : "",
                'keterangan' => $maintenance->lokasi_switch ? '-' . $maintenance->lokasi_switch : '-',
            ];
            }
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Tanggal',
            'Divisi',
            'Node Terpakai',
            'Node Bagus', 
            'Node Rusak',
            'Node Kosong',
            'Switch',
            'Status Net',
            'Petugas',
            'Keterangan'
        ];
    }

    public function title(): string
    {
        return 'MAINTENANCE JARINGAN (MIF.021.00)';
    }

    public function styles(Worksheet $sheet)
    {
        // Set page orientation and margins
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        
        // Create header box
        $sheet->insertNewRowBefore(1, 5);
        
        // Add logo image
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('images/logo.png'));
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setHeight(50); // Adjust height as needed 
        $drawing->setWorksheet($sheet);

        // Merge cells for logo area
        $sheet->mergeCells('A1:A4');
        $sheet->getStyle('A1:A4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension(1)->setRowHeight(15);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(15);
        
        // Main title - MIS PT. RAJAWALI HIYOTO with gray background
        $sheet->mergeCells('B1:I2');
        $sheet->setCellValue('B1', 'MIS – PT. RAJAWALI HIYOTO');
        $sheet->getStyle('B1:I2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'EEEEEE']
            ]
        ]);
        
        // MAINTENANCE JARINGAN title
        $sheet->mergeCells('B3:I3');
        $sheet->setCellValue('B3', 'MAINTENANCE JARINGAN');
        $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // MIF code
        $sheet->mergeCells('B4:I4');
        $sheet->setCellValue('B4', '(MIF.021.00)');
        $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Right side info box headers
        $sheet->setCellValue('J1', 'BULAN');
        $sheet->setCellValue('J2', strtoupper(now()->locale('id')->monthName));
        $sheet->setCellValue('J3', 'TAHUN');
        $sheet->setCellValue('J4', now()->year);
        $sheet->getStyle('J1')->getFont()->setBold(true);
        $sheet->getStyle('J3')->getFont()->setBold(true);
        $sheet->getStyle('J1:J4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('J1:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J1:J4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEEEEE');
        $sheet->getStyle('J3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEEEEE');
        
        // Right side info box values
        $sheet->setCellValue('K1', 'HAL');
        $sheet->setCellValue('K2', '1/1');
        $sheet->setCellValue('K3', 'PETUGAS');
        
        if (!empty($this->petugasList)) {
            $sheet->setCellValue('K4', implode(", ", $this->petugasList));
        } else {
            $sheet->setCellValue('K4', "-");
        }
        
        $sheet->getStyle('K1')->getFont()->setBold(true);
        $sheet->getStyle('K3')->getFont()->setBold(true);
        $sheet->getStyle('K1:K4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('K1:K4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K1:K4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEEEEE');
        $sheet->getStyle('K3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEEEEE');

        // Waktu Pemeriksaan and Node combined header row
        $sheet->setCellValue('A5', 'Waktu Pemeriksaan');
        $sheet->mergeCells('A5:B5');
        $sheet->setCellValue('C5', 'Divisi');
        $sheet->mergeCells('C5:C6');
        $sheet->setCellValue('D5', 'Node');
        $sheet->mergeCells('D5:G5');
        $sheet->setCellValue('H5', 'Switch');
        $sheet->mergeCells('H5:H6');
        $sheet->setCellValue('I5', 'Status Net');
        $sheet->mergeCells('I5:I6');
        $sheet->setCellValue('J5', 'Petugas');
        $sheet->mergeCells('J5:J6');
        $sheet->setCellValue('K5', 'Keterangan');
        $sheet->mergeCells('K5:K6');
        
        // Set bold and center alignment for all merged cells
        $mergedRanges = ['A5:B5', 'C5:C6', 'D5:G5', 'H5:H6', 'I5:I6', 'J5:J6', 'K5:K6'];
        foreach ($mergedRanges as $range) {
            $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
            ]);
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        
        // Sub-header row - match exact labels from image
        $sheet->setCellValue('A6', 'Hari');
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->setCellValue('C6', ''); // Empty Divisi subheader
        $sheet->setCellValue('D6', 'Node Terpakai');
        $sheet->setCellValue('E6', 'Node Bagus');
        $sheet->setCellValue('F6', 'Node Rusak');
        $sheet->setCellValue('G6', 'Node Kosong');
        $sheet->setCellValue('H6', ''); // Empty Switch subheader
        $sheet->setCellValue('I6', ''); // Empty Status Net subheader
        $sheet->setCellValue('J6', ''); // Empty Petugas subheader
        $sheet->setCellValue('K6', ''); // Empty Keterangan subheader
        
        // Style for sub-header row - no fills, only borders
        $sheet->getStyle('A6:K6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);
        $sheet->getStyle('A6:G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6:G6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Set data borders and format
        $dataStartRow = 7;
        $highestRow = $sheet->getHighestRow();
        
        if ($highestRow >= $dataStartRow) {
            // Apply borders to all data cells
            $sheet->getStyle('A' . $dataStartRow . ':K' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Handle merging for repeating columns
            $prevDay = '';
            $prevDate = '';
            $mergeStartRow = $dataStartRow;
            
            for ($row = $dataStartRow; $row <= $highestRow; $row++) {
            $currentDay = $sheet->getCell('A' . $row)->getValue();
            $currentDate = $sheet->getCell('B' . $row)->getValue();
            
            if (($currentDay != $prevDay || $currentDate != $prevDate) && $row > $dataStartRow) {
                if ($row - $mergeStartRow > 1) {
                // Merge cells for previous group
                $sheet->mergeCells('A' . $mergeStartRow . ':A' . ($row - 1));
                $sheet->mergeCells('B' . $mergeStartRow . ':B' . ($row - 1));
                $sheet->mergeCells('J' . $mergeStartRow . ':J' . ($row - 1));
                }
                $mergeStartRow = $row;
            }
            
            // Handle last group
            if ($row == $highestRow && $row - $mergeStartRow > 0) {
                $sheet->mergeCells('A' . $mergeStartRow . ':A' . $row);
                $sheet->mergeCells('B' . $mergeStartRow . ':B' . $row);
                $sheet->mergeCells('J' . $mergeStartRow . ':J' . $row);
            }
            
            $prevDay = $currentDay;
            $prevDate = $currentDate;
            }

            // Set alignments
            $sheet->getStyle('A' . $dataStartRow . ':K' . $highestRow)->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $dataStartRow . ':B' . $highestRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $dataStartRow . ':G' . $highestRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $dataStartRow . ':J' . $highestRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);  // Hari
        $sheet->getColumnDimension('B')->setWidth(12);  // Tanggal
        $sheet->getColumnDimension('C')->setWidth(18);  // Divisi
        $sheet->getColumnDimension('D')->setWidth(12);  // Node Terpakai
        $sheet->getColumnDimension('E')->setWidth(12);  // Node Bagus
        $sheet->getColumnDimension('F')->setWidth(12);  // Node Rusak
        $sheet->getColumnDimension('G')->setWidth(12);  // Node Kosong
        $sheet->getColumnDimension('H')->setWidth(20);  // Switch
        $sheet->getColumnDimension('I')->setWidth(10);  // Status Net
        $sheet->getColumnDimension('J')->setWidth(12);  // Petugas
        $sheet->getColumnDimension('K')->setWidth(24);  // Keterangan
        
        return $sheet;
    }
}