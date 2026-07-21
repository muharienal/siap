<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed Divisions
        $divisi_it = Division::create([
            'name' => 'Divisi IT',
            'description' => 'Divisi Teknologi Informasi'
        ]);

        $divisi_marketing = Division::create([
            'name' => 'Divisi Marketing',
            'description' => 'Divisi Pemasaran'
        ]);

        $divisi_hr = Division::create([
            'name' => 'Divisi HR',
            'description' => 'Divisi Sumber Daya Manusia'
        ]);

        // Seed Positions
        $pos_manager = Position::create([
            'name' => 'Manager',
            'description' => 'Posisi Manager'
        ]);

        $pos_staff = Position::create([
            'name' => 'Staff',
            'description' => 'Posisi Staff'
        ]);

        $pos_admin = Position::create([
            'name' => 'Administrator',
            'description' => 'Posisi Administrator Sistem'
        ]);

        // Seed Users & Employees
        $adminUser = Employee::create([
            'division_id' => $divisi_it->id,
            'position_id' => $pos_admin->id,
            'full_name' => 'Administrator Sistem',
            'nip' => 'ADM001',
            'gender' => 'L',
            'birth_date' => '1990-01-15',
            'phone_number' => '08123456789',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'employment_status' => 'Tetap'
        ]);

        User::create([
            'employee_id' => $adminUser->id,
            'email' => 'admin@siap.com',
            'password' => Hash::make('password'),
            'role' => 1, // Admin
            'is_active' => true
        ]);

        $userUser = Employee::create([
            'division_id' => $divisi_it->id,
            'position_id' => $pos_staff->id,
            'full_name' => 'Ahmad Yusuf',
            'nip' => 'ITD001',
            'gender' => 'L',
            'birth_date' => '1995-05-20',
            'phone_number' => '08987654321',
            'address' => 'Jl. Teknologi No. 10, Jakarta',
            'employment_status' => 'Kontrak'
        ]);


        User::create([
            'employee_id' => $userUser->id,
            'email' => 'user@siap.com',
            'password' => Hash::make('password'),
            'role' => 2, // User
            'is_active' => true
        ]);

        $user2 = Employee::create([
            'division_id' => $divisi_marketing->id,
            'position_id' => $pos_manager->id,
            'full_name' => 'Siti Nurhaliza',
            'nip' => 'MKT001',
            'gender' => 'P',
            'birth_date' => '1992-08-10',
            'phone_number' => '08765432100',
            'address' => 'Jl. Marketing No. 5, Jakarta',
            'employment_status' => 'Tetap'
        ]);

        User::create([
            'employee_id' => $user2->id,
            'email' => 'siti@siap.com',
            'password' => Hash::make('password'),
            'role' => 2,
            'is_active' => true
        ]);

        $user3 =  Employee::create([
            'division_id' => $divisi_hr->id,
            'position_id' => $pos_manager->id,
            'full_name' => 'Budi Santoso',
            'nip' => 'HRD001',
            'gender' => 'L',
            'birth_date' => '1991-03-25',
            'phone_number' => '08654321098',
            'address' => 'Jl. HR No. 8, Jakarta',
            'employment_status' => 'Tetap'
        ]);

        User::create([
            'employee_id' => $user3->id,
            'email' => 'budi@siap.com',
            'password' => Hash::make('password'),
            'role' => 2,
            'is_active' => true
        ]);

        // Seed Rooms
        DB::table('rooms')->insert([
            'name' => 'Ruang Anggrek',
            'capacity' => 20,
            'location' => 'Lantai 1',
            'description' => 'Ruang meeting dengan fasilitas lengkap.',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('rooms')->insert([
            'name' => 'Ruang Dahlia',
            'capacity' => 15,
            'location' => 'Lantai 1',
            'description' => 'Ruang meeting ukuran sedang.',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('rooms')->insert([
            'name' => 'Ruang Semeru',
            'capacity' => 30,
            'location' => 'Lantai 2',
            'description' => 'Ruang seminar dengan kapasitas besar.',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('rooms')->insert([
            'name' => 'Ruang Rooftop',
            'capacity' => 25,
            'location' => 'Lantai 3',
            'description' => 'Ruang outdoor di atas gedung.',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Seed Facilities
        DB::table('facilities')->insert([
            'name' => 'Proyektor',
            'description' => 'Proyektor HD untuk presentasi.',
            'stock_qty' => 5,
            'storage_location' => 'Gudang Lantai 1',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('facilities')->insert([
            'name' => 'Whiteboard',
            'description' => 'Whiteboard besar dengan spidol warna-warni.',
            'stock_qty' => 10,
            'storage_location' => 'Gudang Lantai 1',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('facilities')->insert([
            'name' => 'Konsumsi',
            'description' => 'Makanan dan minuman untuk peserta meeting.',
            'stock_qty' => 50,
            'storage_location' => 'Dapur',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('facilities')->insert([
            'name' => 'Audio System',
            'description' => 'Sistem audio profesional untuk acara besar.',
            'stock_qty' => 2,
            'storage_location' => 'Gudang Lantai 2',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('facilities')->insert([
            'name' => 'Meja Kursi',
            'description' => 'Meja dan kursi untuk setup meeting.',
            'stock_qty' => 20,
            'storage_location' => 'Gudang Lantai 1',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}