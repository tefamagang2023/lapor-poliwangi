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
            'label' => 'Unit',
            'url' => 'admin/index-unit',
            'icon' => 'fas fa-users',
            'active' => serialize(['admin/index-unit','admin/index-unit*']),
            'can' => serialize(['admin']),
            'parent_id' => 7,
            'urut' => 1,
        ]);
        
        Menu::create([
            'id' => 11,
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
