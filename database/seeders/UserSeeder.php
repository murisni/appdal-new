<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan Role sudah ada
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // 1. Akun Super Admin
        $superadmin = User::firstOrCreate(
            ['email' => 'murisni@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('appdal2026'),
            ]
        );
        $superadmin->assignRole('super_admin');

        // 2. Akun Admin (Dinas)
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Dinsos',
                'password' => Hash::make('appdal2026'),
            ]
        );
        $admin->assignRole('admin');

        // 3. Akun User (Kecamatan)
        $daftarKecamatan = ['Selat', 'Kapuas Hilir', 'Bataguh', 'Basarang'];

        foreach ($daftarKecamatan as $kecamatan) {
            // Format email: selat@gmail.com, kapuas_hilir@gmail.com
            $emailKecamatan = strtolower(str_replace(' ', '_', $kecamatan)) . '@gmail.com';

            $userKecamatan = User::firstOrCreate(
                ['email' => $emailKecamatan],
                [
                    'name' => 'Admin Kec. ' . $kecamatan,
                    'password' => Hash::make('appdal2026'),
                    'kecamatan' => $kecamatan,
                ]
            );
            $userKecamatan->assignRole('user');
        }
    }
}
