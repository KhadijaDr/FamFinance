<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenir l'utilisateur actuel
        $user = Auth::user();
        if (!$user) {
            // Si aucun utilisateur n'est connecté, on utilise l'admin par défaut
            $user = User::where('email', 'admin@famfinance.com')->first();
            if (!$user) {
                // Si l'admin n'existe pas, on ne peut pas continuer
                return;
            }
        }

        // Catégories de dépenses
        $expenseCategories = [
            ['name' => 'Alimentation', 'icon' => 'fa-shopping-cart', 'color' => '#4CAF50', 'type' => 'expense'],
            ['name' => 'Logement', 'icon' => 'fa-home', 'color' => '#2196F3', 'type' => 'expense'],
            ['name' => 'Transport', 'icon' => 'fa-car', 'color' => '#FF9800', 'type' => 'expense'],
            ['name' => 'Santé', 'icon' => 'fa-heart', 'color' => '#F44336', 'type' => 'expense'],
            ['name' => 'Éducation', 'icon' => 'fa-book', 'color' => '#9C27B0', 'type' => 'expense'],
            ['name' => 'Loisirs', 'icon' => 'fa-film', 'color' => '#673AB7', 'type' => 'expense'],
            ['name' => 'Vêtements', 'icon' => 'fa-tshirt', 'color' => '#3F51B5', 'type' => 'expense'],
            ['name' => 'Factures', 'icon' => 'fa-file-invoice', 'color' => '#607D8B', 'type' => 'expense'],
            ['name' => 'Abonnements', 'icon' => 'fa-calendar', 'color' => '#E91E63', 'type' => 'expense'],
            ['name' => 'Autres dépenses', 'icon' => 'fa-ellipsis-h', 'color' => '#9E9E9E', 'type' => 'expense'],
        ];

        // Catégories de revenus
        $incomeCategories = [
            ['name' => 'Salaire', 'icon' => 'fa-wallet', 'color' => '#4CAF50', 'type' => 'income'],
            ['name' => 'Freelance', 'icon' => 'fa-laptop', 'color' => '#2196F3', 'type' => 'income'],
            ['name' => 'Investissements', 'icon' => 'fa-chart-line', 'color' => '#FF9800', 'type' => 'income'],
            ['name' => 'Cadeaux', 'icon' => 'fa-gift', 'color' => '#E91E63', 'type' => 'income'],
            ['name' => 'Remboursements', 'icon' => 'fa-undo', 'color' => '#9C27B0', 'type' => 'income'],
            ['name' => 'Autres revenus', 'icon' => 'fa-ellipsis-h', 'color' => '#9E9E9E', 'type' => 'income'],
        ];

        // Fusionner les catégories
        $categories = array_merge($expenseCategories, $incomeCategories);

        // Créer les catégories
        foreach ($categories as $category) {
            // Vérifier si la catégorie existe déjà pour cet utilisateur
            $exists = Category::where('user_id', $user->id)
                ->where('name', $category['name'])
                ->where('type', $category['type'])
                ->exists();
                
            // Si elle n'existe pas, on la crée
            if (!$exists) {
                Category::create([
                    'user_id' => $user->id,
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'type' => $category['type'],
                    'is_system' => true,
                ]);
            }
        }
    }
}
