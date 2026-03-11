<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Toko;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin (Bendahara)
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@ecanteen.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'saldo'    => 0,
        ]);

        // Create Penjual 1 (Toko Makan Siang)
        $penjual1 = User::create([
            'name'     => 'Penjual Makan Siang',
            'email'    => 'penjual1@ecanteen.com',
            'password' => Hash::make('password'),
            'role'     => 'toko',
            'saldo'    => 0,
        ]);

        // Create Penjual 2 (Toko Minuman Segar)
        $penjual2 = User::create([
            'name'     => 'Penjual Minuman Segar',
            'email'    => 'penjual2@ecanteen.com',
            'password' => Hash::make('password'),
            'role'     => 'toko',
            'saldo'    => 0,
        ]);

        // Create Siswa/User Demo
        User::create([
            'name'     => 'User Demo',
            'email'    => 'user@ecanteen.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'saldo'    => 50000,
        ]);

        // Create Toko 1 (owned by penjual1)
        $toko1 = Toko::create([
            'nama_toko' => 'Toko Makan Siang',
            'user_id'   => $penjual1->id,
        ]);

        // Create Menus for Toko 1
        Menu::create(['toko_id' => $toko1->id, 'nama_menu' => 'Nasi Goreng',  'kategori' => 'Makanan Berat', 'harga' => 15000, 'status' => 'tersedia']);
        Menu::create(['toko_id' => $toko1->id, 'nama_menu' => 'Mie Goreng',   'kategori' => 'Makanan Berat', 'harga' => 12000, 'status' => 'tersedia']);
        Menu::create(['toko_id' => $toko1->id, 'nama_menu' => 'Ayam Geprek',  'kategori' => 'Lauk',          'harga' => 18000, 'status' => 'tersedia']);

        // Create Toko 2 (owned by penjual2)
        $toko2 = Toko::create([
            'nama_toko' => 'Toko Minuman Segar',
            'user_id'   => $penjual2->id,
        ]);

        // Create Menus for Toko 2
        Menu::create(['toko_id' => $toko2->id, 'nama_menu' => 'Es Teh Manis', 'kategori' => 'Minuman',  'harga' =>  5000, 'status' => 'tersedia']);
        Menu::create(['toko_id' => $toko2->id, 'nama_menu' => 'Es Jeruk',     'kategori' => 'Minuman',  'harga' =>  7000, 'status' => 'tersedia']);
        Menu::create(['toko_id' => $toko2->id, 'nama_menu' => 'Jus Alpukat',  'kategori' => 'Minuman',  'harga' => 10000, 'status' => 'tersedia']);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin    - admin@ecanteen.com    / password');
        $this->command->info('Penjual1 - penjual1@ecanteen.com / password  (Toko Makan Siang)');
        $this->command->info('Penjual2 - penjual2@ecanteen.com / password  (Toko Minuman Segar)');
        $this->command->info('Siswa    - user@ecanteen.com     / password');
    }
}
