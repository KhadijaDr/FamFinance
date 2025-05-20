<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Category::where('user_id', $user->id)
            ->withCount('transactions');
            
        // Filtrage par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
            
        // Recherche par nom
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Paginer les résultats - 15 catégories par page
        $categories = $query->paginate(15)->withQueryString();
        
        // Calculer les totaux par catégorie
        foreach ($categories as $category) {
            $totalIncome = Transaction::where('user_id', $user->id)
                ->where('category_id', $category->id)
                ->where('type', 'income')
                ->sum('amount');
                
            $totalExpense = Transaction::where('user_id', $user->id)
                ->where('category_id', $category->id)
                ->where('type', 'expense')
                ->sum('amount');
                
            $category->total_income = $totalIncome;
            $category->total_expense = $totalExpense;
            $category->net_amount = $totalIncome - $totalExpense;
        }
        
        return view('categories.index', compact('categories'));
    }
    
    /**
     * Affiche le formulaire de création d'une catégorie.
     */
    public function create(): View
    {
        // Liste de couleurs pour le sélecteur
        $colors = [
            'red' => 'Rouge',
            'orange' => 'Orange',
            'yellow' => 'Jaune',
            'green' => 'Vert',
            'teal' => 'Turquoise',
            'blue' => 'Bleu',
            'indigo' => 'Indigo',
            'purple' => 'Violet',
            'pink' => 'Rose',
            'gray' => 'Gris',
        ];
        
        return view('categories.create', compact('colors'));
    }
    
    /**
     * Enregistre une nouvelle catégorie.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'color' => 'required|string',
            'icon' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        // Vérifier si le nom de la catégorie existe déjà pour cet utilisateur
        $existingCategory = Category::where('user_id', $user->id)
            ->where('name', $request->name)
            ->first();
            
        if ($existingCategory) {
            return redirect()->back()->withErrors([
                'name' => 'Vous avez déjà une catégorie avec ce nom.'
            ])->withInput();
        }
        
        Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }
    
    /**
     * Affiche les détails d'une catégorie.
     */
    public function show(Category $category): View
    {
        // Vérifier que la catégorie appartient à l'utilisateur connecté
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette catégorie.');
        }
        
        // Récupérer les 10 dernières transactions de cette catégorie
        $recentTransactions = Transaction::where('category_id', $category->id)
            ->orderByDesc('transaction_date')
            ->limit(10)
            ->get();
            
        // Calculer les totaux
        $totalIncome = Transaction::where('category_id', $category->id)
            ->where('type', 'income')
            ->sum('amount');
            
        $totalExpense = Transaction::where('category_id', $category->id)
            ->where('type', 'expense')
            ->sum('amount');
            
        $netAmount = $totalIncome - $totalExpense;
        
        // Compter le nombre total de transactions
        $transactions_count = Transaction::where('category_id', $category->id)->count();
        $category->transactions_count = $transactions_count;
        
        return view('categories.show', compact(
            'category',
            'recentTransactions',
            'totalIncome',
            'totalExpense',
            'netAmount'
        ));
    }
    
    /**
     * Affiche le formulaire de modification d'une catégorie.
     */
    public function edit(Category $category): View
    {
        // Vérifier que la catégorie appartient à l'utilisateur connecté
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette catégorie.');
        }
        
        // Liste de couleurs pour le sélecteur
        $colors = [
            'red' => 'Rouge',
            'orange' => 'Orange',
            'yellow' => 'Jaune',
            'green' => 'Vert',
            'teal' => 'Turquoise',
            'blue' => 'Bleu',
            'indigo' => 'Indigo',
            'purple' => 'Violet',
            'pink' => 'Rose',
            'gray' => 'Gris',
        ];
        
        return view('categories.edit', compact('category', 'colors'));
    }
    
    /**
     * Met à jour une catégorie.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // Vérifier que la catégorie appartient à l'utilisateur connecté
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette catégorie.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'color' => 'required|string',
            'icon' => 'nullable|string',
        ]);
        
        // Vérifier si le nom de la catégorie existe déjà pour cet utilisateur
        $existingCategory = Category::where('user_id', $category->user_id)
            ->where('name', $request->name)
            ->where('id', '!=', $category->id)
            ->first();
            
        if ($existingCategory) {
            return redirect()->back()->withErrors([
                'name' => 'Vous avez déjà une catégorie avec ce nom.'
            ])->withInput();
        }
        
        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }
    
    /**
     * Supprime une catégorie.
     */
    public function destroy(Request $request, Category $category): RedirectResponse
    {
        // Vérifier que la catégorie appartient à l'utilisateur connecté
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette catégorie.');
        }
        
        // Vérifier si la catégorie a des transactions
        $hasTransactions = Transaction::where('category_id', $category->id)->exists();
        
        if ($hasTransactions) {
            // Si une catégorie par défaut est spécifiée, transférer les transactions
            if ($request->has('transfer_to') && $request->transfer_to) {
                Transaction::where('category_id', $category->id)
                    ->update(['category_id' => $request->transfer_to]);
            } else {
                // Sinon, définir la catégorie des transactions à null
                Transaction::where('category_id', $category->id)
                    ->update(['category_id' => null]);
            }
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
    
    /**
     * Initialise les catégories par défaut pour l'utilisateur actuel.
     */
    public function initializeDefault(): RedirectResponse
    {
        $user = Auth::user();
        
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
        
        $created = 0;

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
                $created++;
            }
        }
        
        if ($created > 0) {
            return redirect()->route('categories.index')
                ->with('success', $created . ' catégories par défaut ont été créées avec succès.');
        } else {
            return redirect()->route('categories.index')
                ->with('info', 'Toutes les catégories par défaut existent déjà.');
        }
    }
}