<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\Position;

class DivisionPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create divisions
        $divisions = [
            ['name' => 'IT & Digital', 'description' => 'Teknologi Informasi dan Digitalisasi'],
            ['name' => 'Human Resources', 'description' => 'Sumber Daya Manusia'],
            ['name' => 'Finance & Accounting', 'description' => 'Keuangan dan Akuntansi'],
            ['name' => 'Marketing & Sales', 'description' => 'Pemasaran dan Penjualan'],
            ['name' => 'Operations', 'description' => 'Operasional'],
            ['name' => 'Legal & Compliance', 'description' => 'Legal dan Kepatuhan'],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(['name' => $division['name']], $division);
        }

        // Create positions
        $positions = [
            ['name' => 'Manager', 'level' => 1, 'description' => 'Manajer Divisi'],
            ['name' => 'Supervisor', 'level' => 2, 'description' => 'Supervisor'],
            ['name' => 'Senior Staff', 'level' => 3, 'description' => 'Staff Senior'],
            ['name' => 'Staff', 'level' => 4, 'description' => 'Staff'],
            ['name' => 'Junior Staff', 'level' => 5, 'description' => 'Staff Junior'],
            ['name' => 'Intern', 'level' => 6, 'description' => 'Magang'],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(['name' => $position['name']], $position);
        }
    }
}