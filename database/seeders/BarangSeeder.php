<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'LENOVO H520S',
                'serial' => json_encode(["cpu" => "8CG8165RKHDS", "monitor" => "CNC819KSZ385"]),
                'operating_system' => 'Windows 7',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i3-3240@340ghz", "RAM" => "2GB DDR3", "Storage" => "500GB HDD"]),
                'kelayakan' => 90,
                'tahun_perolehan' => '2017-07-01',
                'status' => 'Backup',
                'created_at' => Carbon::now()->subMonths(10),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'HP EliteDesk 800',
                'serial' => json_encode(["cpu" => "7HG7283FJKL", "monitor" => "CNC7283XH567"]),
                'operating_system' => 'Windows 10',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i5-6500@3.2GHz", "RAM" => "8GB DDR4", "Storage" => "256GB SSD"]),
                'kelayakan' => 85,
                'tahun_perolehan' => '2018-06-15',
                'status' => 'Aktif',
                'created_at' => Carbon::now()->subMonths(9),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'DELL OptiPlex 3050',
                'serial' => json_encode(["cpu" => "9YG9182JHYZ", "monitor" => "CNC9182KH849"]),
                'operating_system' => 'Windows 11',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i7-7700@3.6GHz", "RAM" => "16GB DDR4", "Storage" => "512GB SSD"]),
                'kelayakan' => 95,
                'tahun_perolehan' => '2019-05-20',
                'status' => 'Aktif',
                'created_at' => Carbon::now()->subMonths(8),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'ASUS ExpertCenter D5',
                'serial' => json_encode(["cpu" => "6TG5189MLXP", "monitor" => "CNC5189GK902"]),
                'operating_system' => 'Windows 10',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i5-10400@2.9GHz", "RAM" => "8GB DDR4", "Storage" => "1TB HDD"]),
                'kelayakan' => 80,
                'tahun_perolehan' => '2020-03-10',
                'status' => 'Backup',
                'created_at' => Carbon::now()->subMonths(7),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'Acer Veriton M4660G',
                'serial' => json_encode(["cpu" => "4QR2189MZXK", "monitor" => "CNC2189FLB76"]),
                'operating_system' => 'Windows 7',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i3-9100@3.6GHz", "RAM" => "4GB DDR4", "Storage" => "500GB HDD"]),
                'kelayakan' => 70,
                'tahun_perolehan' => '2016-11-05',
                'status' => 'Pemusnahan',
                'created_at' => Carbon::now()->subMonths(6),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'LENOVO ThinkCentre M720s',
                'serial' => json_encode(["cpu" => "3ZX9182HYNM", "monitor" => "CNC9182NKX21"]),
                'operating_system' => 'Windows 11',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i7-9700@3.0GHz", "RAM" => "16GB DDR4", "Storage" => "512GB SSD"]),
                'kelayakan' => 92,
                'tahun_perolehan' => '2021-02-18',
                'status' => 'Aktif',
                'created_at' => Carbon::now()->subMonths(5),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'DELL Vostro 3681',
                'serial' => json_encode(["cpu" => "2MX9182LKVP", "monitor" => "CNC9182GTL98"]),
                'operating_system' => 'Windows 10',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i5-9500@3.0GHz", "RAM" => "8GB DDR4", "Storage" => "1TB HDD"]),
                'kelayakan' => 85,
                'tahun_perolehan' => '2020-09-12',
                'status' => 'Backup',
                'created_at' => Carbon::now()->subMonths(4),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'HP ProDesk 400 G6',
                'serial' => json_encode(["cpu" => "1LP9182JKXT", "monitor" => "CNC9182TGY56"]),
                'operating_system' => 'Windows 10',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i3-8100@3.6GHz", "RAM" => "8GB DDR4", "Storage" => "256GB SSD"]),
                'kelayakan' => 88,
                'tahun_perolehan' => '2019-07-08',
                'status' => 'Aktif',
                'created_at' => Carbon::now()->subMonths(3),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'Acer Aspire TC-885',
                'serial' => json_encode(["cpu" => "0KP9182BNZX", "monitor" => "CNC9182HJY98"]),
                'operating_system' => 'Windows 11',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i7-8700@3.2GHz", "RAM" => "16GB DDR4", "Storage" => "1TB SSD"]),
                'kelayakan' => 97,
                'tahun_perolehan' => '2022-01-25',
                'status' => 'Aktif',
                'created_at' => Carbon::now()->subMonths(2),
            ],
            [
                'jenis_barang' => 'Komputer',
                'model' => 'PC',
                'tipe_merk' => 'MSI PRO DP21',
                'serial' => json_encode(["cpu" => "9XP9182DLQP", "monitor" => "CNC9182ZXB43"]),
                'operating_system' => 'Windows 11',
                'spesifikasi' => json_encode(["Processor" => "Intel Core i5-1135G7@2.4GHz", "RAM" => "8GB DDR4", "Storage" => "512GB SSD"]),
                'kelayakan' => 90,
                'tahun_perolehan' => '2022-11-30',
                'status' => 'Aktif',
                'created_at' => Carbon::now()->subMonth(),
            ],
        ];
        
        // Insert ke database
        DB::table('tbl_barang')->insert($data);
    }
}
