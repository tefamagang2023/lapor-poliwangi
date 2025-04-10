<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\Menu;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::truncate();
        Menu::create([
            'id' => 1,
            'modul' => 'Core',
            'label' => 'Master',
            'url' => '',
            'can' => serialize(['admin']),
            'icon' => 'fas fa-columns',
            'urut' => 1,
            'parent_id' => 0,
            'active' => '',
        ]);
        Menu::create([
            'id' => 2,
            'modul' => 'Core',
            'label' => 'User',
            'url' => 'users',
            'can' => serialize(['admin']),
            'icon' => 'fas fa-fw fa-users',
            'urut' => 1,
            'parent_id' => 1,
            'active' => serialize(['users','users*']),
        ]);
        Menu::create([
            'id' => 3,
            'modul' => 'Core',
            'label' => 'Menu',
            'url' => 'menus',
            'can' => serialize(['admin']),
            'icon' => 'fas fa-bars',
            'urut' => 2,
            'parent_id' => 1,
            'active' => serialize(['menus','menus*']),
        ]);
        Menu::create([
            'id' => 4,
            'modul' => 'Core',
            'label' => 'Roles & Permisions',
            'url' => '',
            'can' => serialize(['admin']),
            'icon' => 'fas fa-address-card',
            'urut' => 2,
            'parent_id' => 0,
            'active' => '',
        ]);
        Menu::create([
            'id' => 5,
            'modul' => 'Core',
            'label' => 'Roles',
            'url' => 'roles',
            'can' => serialize(['admin']),
            'icon' => 'far fa-circle',
            'urut' => 1,
            'parent_id' => 4,
            'active' => serialize(['roles','roles*']),
        ]);
        Menu::create([
            'id' => 6,
            'modul' => 'Core',
            'label' => 'Permissions',
            'url' => 'permissions',
            'can' => serialize(['admin']),
            'icon' => 'far fa-circle',
            'urut' => 2,
            'parent_id' => 4,
            'active' => serialize(['permissions','permissions*']),
        ]);
        Menu::create([
            'id' => 7,
            'modul' => 'Core',
            'label' => 'Dashboard',
            'url' => '/dashboard-admin',
            'icon' => 'fas fa-home',
            'active' => serialize(['/dashboard-admin','/dashboard-admin*']),
            'can' => serialize(['admin']),
            'parent_id' => 0,
            'urut' => 3,
        ]);
        
        Menu::create([
            'id' => 8,
            'modul' => 'Core',
            'label' => 'Pesan Masuk Pelapor',
            'url' => 'admin/pesan-masuk-pelapor',
            'icon' => 'far fa-envelope',
            'active' => serialize(['admin/pesan-masuk-pelapor','admin/pesan-masuk-pelapor*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 3,
        ]);
        
        Menu::create([
            'id' => 9,
            'modul' => 'Core',
            'label' => 'Pesan Keluar Pelapor',
            'url' => 'admin/pesan-keluar-pelapor',
            'icon' => 'far fa-envelope-open',
            'active' => serialize(['admin/pesan-keluar-pelapor','admin/pesan-keluar-pelapor*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 4,
        ]);
        
        Menu::create([
            'id' => 10,
            'modul' => 'Core',
            'label' => 'Pesan Masuk Unit',
            'url' => 'admin/pesan-masuk-unit',
            'icon' => 'fas fa-envelope',
            'active' => serialize(['/pesan-masuk-unit','/pesan-masuk-unit*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 5,
        ]);
        
        Menu::create([
            'id' => 11,
            'modul' => 'Core',
            'label' => 'Pesan Keluar Unit',
            'url' => 'admin/pesan-keluar-unit',
            'icon' => 'fas fa-envelope-open',
            'active' => serialize(['admin/pesan-keluar-unit','admin/pesan-keluar-unit*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 6,
        ]);
        
        Menu::create([
            'id' => 12,
            'modul' => 'Core',
            'label' => 'Dashboard Pelapor',
            'url' => '/dashboard-pelapor',
            'icon' => 'fas fa-home',
            'active' => serialize(['/dashboard-pelapor','/dashboard-pelapor*']),
            'can' => serialize(['Pelapor']),
            'parent_id' => 0,
            'urut' => 2,
        ]);
        
        Menu::create([
            'id' => 13,
            'modul' => 'Core',
            'label' => 'Pesan Masuk',
            'url' => 'pelapor/pesan-masuk',
            'icon' => 'fas fa-envelope',
            'active' => serialize(['pelapor/pesan-masuk','pelapor/pesan-masuk*']),
            'can' => serialize(['Pelapor']),
            'parent_id' => 12,
            'urut' => 3,
        ]);
        
        Menu::create([
            'id' => 14,
            'modul' => 'Core',
            'label' => 'Pesan Keluar',
            'url' => 'pelapor/pesan-keluar',
            'icon' => 'fas fa-envelope-open',
            'active' => serialize(['pelapor/pesan-keluar','pelapor/pesan-keluar*']),
            'can' => serialize(['Pelapor']),
            'parent_id' => 12,
            'urut' => 4,
        ]);
        
        Menu::create([
            'id' => 15,
            'modul' => 'Core',
            'label' => 'Tambah Laporan',
            'url' => 'pelapor/tambah-laporan',
            'icon' => 'fas fa-clipboard',
            'active' => serialize(['pelapor/tambah-laporan','pelapor/tambah-laporan*']),
            'can' => serialize(['Pelapor']),
            'parent_id' => 12,
            'urut' => 2,
        ]);
        
        Menu::create([
            'id' => 16,
            'modul' => 'Core',
            'label' => 'Pesan Masuk',
            'url' => 'UnitPoliwangi/pesan-masuk-unit',
            'icon' => 'fas fa-envelope',
            'active' => serialize(['UnitPoliwangi/pesan-masuk-unit','UnitPoliwangi/pesan-masuk-unit*']),
            'can' => serialize(['Unit']),
            'parent_id' => 18,
            'urut' => 1,
        ]);
        
        Menu::create([
            'id' => 17,
            'modul' => 'Core',
            'label' => 'Pesan Keluar',
            'url' => 'UnitPoliwangi/pesan-keluar-unit',
            'icon' => 'fas fa-envelope-open',
            'active' => serialize(['UnitPoliwangi/pesan-keluar-unit','UnitPoliwangi/pesan-keluar-unit*']),
            'can' => serialize(['Unit']),
            'parent_id' => 18,
            'urut' => 2,
        ]);
        
        Menu::create([
            'id' => 18,
            'modul' => 'Core',
            'label' => 'Dashboard Unit',
            'url' => '/dashboard-unit',
            'icon' => 'fas fa-home',
            'active' => serialize(['/dashboard-unit','/dashboard-unit*']),
            'can' => serialize(['Unit']),
            'parent_id' => 0,
            'urut' => 1,
        ]);
        
        Menu::create([
            'id' => 19,
            'modul' => 'Core',
            'label' => 'Pesan',
            'url' => 'pelapor/pesan',
            'icon' => 'fas fa-envelope-square',
            'active' => serialize(['pelapor/pesan','pelapor/pesan*']),
            'can' => serialize(['Pelapor']),
            'parent_id' => 12,
            'urut' => 1,
        ]);
        
        Menu::create([
            'id' => 22,
            'modul' => 'Core',
            'label' => 'Unit',
            'url' => 'admin/index-unit',
            'icon' => 'fas fa-users',
            'active' => serialize(['admin/index-unit','admin/index-unit*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 1,
        ]);
        
        Menu::create([
            'id' => 23,
            'modul' => 'Core',
            'label' => 'Pesan',
            'url' => 'admin/pesan',
            'icon' => 'fas fa-envelope-square',
            'active' => serialize(['admin/pesan','admin/pesan*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 2,
        ]);
    }
}
