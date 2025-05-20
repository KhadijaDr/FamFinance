<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les rôles
        $adminRole = Role::create(['name' => 'admin']);
        $memberRole = Role::create(['name' => 'member']);
        $childRole = Role::create(['name' => 'child']);

        // Créer un utilisateur administrateur
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@famfinance.com',
            'password' => bcrypt('admin123'),
        ]);
        $admin->assignRole($adminRole);

        // Créer un utilisateur test
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('test123'),
        ]);
        $user->assignRole($memberRole);

        // Exécuter les autres seeders
        $this->call([
            CategorySeeder::class,
        ]);
    }
}
