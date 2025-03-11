<?php

namespace App\Exports;

use App\Models\Maintenance;
use App\Models\MenuAktif;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SwitchMaintenanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
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

        foreach ($maintenances as $maintenance) {
            $barang = $maintenance->barang;
            if (!$barang || !$barang->menuAktif->count()) {
                continue;
            }

            $date = Carbon::parse($maintenance->tgl_maintenance);
            $dateFormatted = $date->format('d-M-y');
            
            $dayOfWeek = ($currentDate != $dateFormatted) ? 
                strtoupper($date->locale('id')->dayName) : '';
            $currentDate = $dateFormatted;

            foreach ($barang->menuAktif as $menuAktif) {
                $result[] = [
                    'hari' => $dayOfWeek,
                    'tanggal' => $dateFormatted,
                    'divisi' => $menuAktif->departemen->nama_departemen ?? '',
                    'node_terpakai' => $maintenance->node_terpakai ?? 0,
                    'node_bagus' => $maintenance->node_bagus ?? 0,
                    'node_rusak' => $maintenance->node_rusak ?? 0,
                    'node_kosong' => ($maintenance->node_bagus ?? 0) - ($maintenance->node_terpakai ?? 0),
                    'switch' => $barang->tipe_merk ?? '',
                    'status_net' => $maintenance->status_net ?? '',
                    'petugas' => !empty($this->petugasList) ? implode(", ", $this->petugasList) : "-",
                    'keterangan' => $maintenance->lokasi_switch ?? $menuAktif->lokasi->nama_lokasi ?? ''
                ];
            }
        }

        if (empty($result)) {
            Log::warning('No data found for export with the current filters');
            return collect([[
                'hari' => '', 'tanggal' => '', 'divisi' => '',
                'node_terpakai' => '', 'node_bagus' => '', 'node_rusak' => '',
                'node_kosong' => '', 'switch' => '', 'status_net' => '',
                'petugas' => '', 'keterangan' => ''
            ]]);
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
        // Add Logo - Big A
        $sheet->setCellValue('A1', 'A');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Set title - MIS PT. RAJAWALI HIYOTO
        $sheet->mergeCells('B1:I1');
        $sheet->setCellValue('B1', 'MIS – PT. RAJAWALI HIYOTO');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add subtitle - MAINTENANCE JARINGAN
        $sheet->mergeCells('B2:I2');
        $sheet->setCellValue('B2', 'MAINTENANCE JARINGAN');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add MIF code on next line
        $sheet->mergeCells('B3:I3');
        $sheet->setCellValue('B3', '(MIF.021.00)');
        $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add month, year, and petugas info
        $sheet->setCellValue('J1', 'BULAN');
        $sheet->setCellValue('K1', strtoupper(Carbon::now()->format('F')));
        $sheet->setCellValue('J2', 'TAHUN');
        $sheet->setCellValue('K2', Carbon::now()->format('Y'));
        $sheet->setCellValue('J3', 'PETUGAS');
        
        if (!empty($this->petugasList)) {
            $sheet->setCellValue('K3', implode(", ", $this->petugasList));
        } else {
            $sheet->setCellValue('K3', '-');
        }
        
        // Style the info cells
        $sheet->getStyle('J1:J3')->getFont()->setBold(true);
        $sheet->getStyle('J1:K3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Move data down to make room for title and info
        $sheet->insertNewRowBefore(4, 2);
        
        // Add main headers
        $sheet->setCellValue('A5', 'Waktu Pemeriksaan');
        $sheet->mergeCells('A5:B5');
        $sheet->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet->getStyle('A5:B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:B5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('C5', 'Divisi');
        $sheet->getStyle('C5')->getFont()->setBold(true);
        $sheet->getStyle('C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('D5', 'Node');
        $sheet->mergeCells('D5:G5');
        $sheet->getStyle('D5:G5')->getFont()->setBold(true);
        $sheet->getStyle('D5:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:G5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('H5', 'Switch');
        $sheet->getStyle('H5')->getFont()->setBold(true);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('I5', 'Status Net');
        $sheet->getStyle('I5')->getFont()->setBold(true);
        $sheet->getStyle('I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('J5', 'Petugas');
        $sheet->getStyle('J5')->getFont()->setBold(true);
        $sheet->getStyle('J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('K5', 'Keterangan');
        $sheet->getStyle('K5')->getFont()->setBold(true);
        $sheet->getStyle('K5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Add sub-headers for waktu pemeriksaan
        $sheet->setCellValue('A6', 'Hari');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->getStyle('B6')->getFont()->setBold(true);
        $sheet->getStyle('B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Add sub-headers for Node
        $sheet->setCellValue('D6', 'Node Terpakai');
        $sheet->getStyle('D6')->getFont()->setBold(true);
        $sheet->getStyle('D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('E6', 'Node Bagus');
        $sheet->getStyle('E6')->getFont()->setBold(true);
        $sheet->getStyle('E6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('F6', 'Node Rusak');
        $sheet->getStyle('F6')->getFont()->setBold(true);
        $sheet->getStyle('F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('G6', 'Node Kosong');
        $sheet->getStyle('G6')->getFont()->setBold(true);
        $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Repeat C for Divisi in row 6
        $sheet->setCellValue('C6', '');
        $sheet->getStyle('C6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Repeat the rest of the headers in row 6
        $sheet->setCellValue('H6', '');
        $sheet->getStyle('H6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('I6', '');
        $sheet->getStyle('I6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('J6', '');
        $sheet->getStyle('J6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->setCellValue('K6', '');
        $sheet->getStyle('K6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Set data borders and format
        $dataStartRow = 7;
        $highestRow = $sheet->getHighestRow();
        
        if ($highestRow >= $dataStartRow) {
            $dataRange = 'A7:K' . $highestRow;
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Set alignment for data
            $sheet->getStyle('A7:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B7:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D7:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I7:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);  // Hari
        $sheet->getColumnDimension('B')->setWidth(12);  // Tanggal
        $sheet->getColumnDimension('C')->setWidth(15);  // Divisi
        $sheet->getColumnDimension('D')->setWidth(15);  // Node Terpakai
        $sheet->getColumnDimension('E')->setWidth(15);  // Node Bagus
        $sheet->getColumnDimension('F')->setWidth(15);  // Node Rusak
        $sheet->getColumnDimension('G')->setWidth(15);  // Node Kosong
        $sheet->getColumnDimension('H')->setWidth(20);  // Switch
        $sheet->getColumnDimension('I')->setWidth(12);  // Status Net
        $sheet->getColumnDimension('J')->setWidth(20);  // Petugas
        $sheet->getColumnDimension('K')->setWidth(20);  // Keterangan
        
        return $sheet;
    }
}