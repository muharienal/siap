<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DivisionsTableSeeder::class,
            PositionsTableSeeder::class,
            UsersTableSeeder::class,
            RoomsTableSeeder::class,
            FacilitiesTableSeeder::class,
        ]);
    }
}

class DivisionsTableSeeder extends Seeder
{
    public function run()
    {
        $divisions = [
            ['id' => 1, 'name' => 'Divisi IT', 'description' => 'Divisi Teknologi Informasi'],
            ['id' => 2, 'name' => 'Divisi Marketing', 'description' => 'Divisi Pemasaran'],
            ['id' => 3, 'name' => 'Divisi HR', 'description' => 'Divisi Sumber Daya Manusia'],
        ];

        DB::table('divisions')->insert($divisions);
    }
}

class PositionsTableSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['id' => 1, 'name' => 'Manager', 'description' => 'Posisi Manager'],
            ['id' => 2, 'name' => 'Staff', 'description' => 'Posisi Staff'],
            ['id' => 3, 'name' => 'Administrator', 'description' => 'Posisi Administrator Sistem'],
        ];

        DB::table('positions')->insert($positions);
    }
}

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'nip' => 'ADM001',
                'full_name' => 'Administrator Sistem',
                'division_id' => 1,
                'position_id' => 3,
                'gender' => 'L',
                'birth_date' => '1990-01-15',
                'phone_number' => '08123456789',
                'email' => null,
                'address' => 'Jl. Admin No. 1, Jakarta',
                'employment_status' => 'Tetap',
                'photo_path' => null,
                'password' => Hash::make('password'),
                'role' => 1, // Admin
                'is_active' => 1,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nip' => 'ITD001',
                'full_name' => 'Ahmad Yusuf',
                'division_id' => 1,
                'position_id' => 2,
                'gender' => 'L',
                'birth_date' => '1995-05-20',
                'phone_number' => '08987654321',
                'email' => null,
                'address' => 'Jl. Teknologi No. 10, Jakarta',
                'employment_status' => 'Kontrak',
                'photo_path' => null,
                'password' => Hash::make('password'),
                'role' => 2, // User
                'is_active' => 1,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nip' => 'MKT001',
                'full_name' => 'Siti Nurhaliza',
                'division_id' => 2,
                'position_id' => 1,
                'gender' => 'P',
                'birth_date' => '1992-08-10',
                'phone_number' => '08765432100',
                'email' => null,
                'address' => 'Jl. Marketing No. 5, Jakarta',
                'employment_status' => 'Tetap',
                'photo_path' => null,
                'password' => Hash::make('password'),
                'role' => 2,
                'is_active' => 1,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nip' => 'HRD001',
                'full_name' => 'Budi Santoso',
                'division_id' => 3,
                'position_id' => 1,
                'gender' => 'L',
                'birth_date' => '1991-03-25',
                'phone_number' => '08654321098',
                'email' => null,
                'address' => 'Jl. HR No. 8, Jakarta',
                'employment_status' => 'Tetap',
                'photo_path' => null,
                'password' => Hash::make('password'),
                'role' => 2,
                'is_active' => 1,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}

class RoomsTableSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'id' => 1,
                'name' => 'Ruang Arjuno',
                'description' => 'Ruang meeting dengan fasilitas lengkap.',
                'capacity' => 15,
                'location' => 'Lantai 1',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Ruang Dahlia',
                'description' => 'Ruang meeting ukuran sedang.',
                'capacity' => 15,
                'location' => 'Lantai 1',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Ruang Semeru',
                'description' => 'Ruang seminar dengan kapasitas besar.',
                'capacity' => 30,
                'location' => 'Lantai 2',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Ruang Rooftop',
                'description' => 'Ruang outdoor di atas gedung.',
                'capacity' => 25,
                'location' => 'Lantai 3',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('rooms')->insert($rooms);
    }
}

class FacilitiesTableSeeder extends Seeder
{
    public function run()
    {
        $facilities = [
            [
                'id' => 1,
                'name' => 'Proyektor',
                'description' => 'Proyektor HD untuk presentasi.',
                'storage_location' => 'Gudang Lantai 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Whiteboard',
                'description' => 'Whiteboard besar dengan spidol warna-warni.',
                'storage_location' => 'Gudang Lantai 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Konsumsi',
                'description' => 'Makanan dan minuman untuk peserta meeting.',
                'storage_location' => 'Umum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Audio',
                'description' => 'Sistem audio profesional untuk acara besar.',
                'storage_location' => 'Gudang Lantai 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Meja Kursi',
                'description' => 'Meja dan kursi untuk setup meeting.',
                'storage_location' => 'Gudang Lantai 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('facilities')->insert($facilities);
    }
}