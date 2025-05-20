<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillController extends Controller
{
    /**
     * Affiche la liste des factures.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Filtrage
        $query = Bill::where('user_id', $user->id);
        
        if ($request->has('status')) {
            if ($request->status === 'paid') {
                $query->where('status', 'paid');
            } elseif ($request->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->status === 'overdue') {
                $query->where('status', 'overdue');
            }
        } else {
            // Par défaut, afficher toutes les factures
            $request->merge(['status' => 'all']);
        }
        
        // Filtrage par date d'échéance
        if ($request->has('due_date_from') && $request->due_date_from) {
            $query->where('due_date', '>=', $request->due_date_from);
        }
        
        if ($request->has('due_date_to') && $request->due_date_to) {
            $query->where('due_date', '<=', $request->due_date_to);
        }
        
        // Tri
        $sortField = $request->sort_by ?? 'due_date';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);
        
        $bills = $query->paginate(15);
        
        // Calculer les statistiques
        $totalBills = $bills->total();
        $totalAmount = $bills->sum('amount');
        $pendingBillsCount = Bill::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $overdueBillsCount = Bill::where('user_id', $user->id)
            ->where('status', 'overdue')
            ->count();
        
        // Calculer le montant mensuel récurrent
        $monthlyRecurringAmount = Bill::where('user_id', $user->id)
            ->where('frequency', 'monthly')
            ->where('status', '!=', 'paid')
            ->sum('amount');
        
        // Récupérer les factures à venir pour les 7 prochains jours
        $upcomingBills = Bill::where('user_id', $user->id)
            ->where('status', '!=', 'paid')
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->orderBy('due_date')
            ->get();
        
        // Récupérer les catégories pour le filtre
        $categories = Category::where('user_id', $user->id)->get();
        
        return view('bills.index', compact(
            'bills',
            'totalBills',
            'totalAmount',
            'pendingBillsCount',
            'overdueBillsCount',
            'monthlyRecurringAmount',
            'upcomingBills',
            'categories'
        ));
    }
    
    /**
     * Affiche le formulaire de création d'une facture.
     */
    public function create(): View
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        
        // Préparer les options de récurrence
        $recurrenceOptions = [
            'once' => 'Unique',
            'monthly' => 'Mensuelle',
            'yearly' => 'Annuelle',
        ];
        
        return view('bills.create', compact('categories', 'recurrenceOptions'));
    }
    
    /**
     * Enregistre une nouvelle facture.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'frequency' => 'required|in:once,monthly,yearly',
            'payment_method' => 'nullable|string',
            'auto_pay' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        $dueDate = Carbon::parse($request->due_date);
        
        // Déterminer le statut en fonction de la date d'échéance
        $status = 'pending';
        if ($dueDate->isPast()) {
            $status = 'overdue';
        }
        
        Bill::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'category_id' => $request->category_id,
            'frequency' => $request->frequency,
            'payment_method' => $request->payment_method,
            'auto_pay' => $request->auto_pay ? true : false,
            'status' => $status,
            'is_recurring' => $request->frequency !== 'once',
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('bills.index')
            ->with('success', 'Facture ajoutée avec succès.');
    }
    
    /**
     * Affiche les détails d'une facture.
     */
    public function show(Bill $bill): View
    {
        $this->authorize('view', $bill);
        
        // Récupérer l'historique des paiements liés à cette facture
        $paymentHistory = Transaction::where('user_id', $bill->user_id)
            ->where('type', 'expense')
            ->where('description', 'like', '%' . $bill->name . '%')
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        return view('bills.show', compact('bill', 'paymentHistory'));
    }
    
    /**
     * Affiche le formulaire de modification d'une facture.
     */
    public function edit(Bill $bill): View
    {
        $this->authorize('update', $bill);
        
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        
        // Préparer les options de récurrence
        $recurrenceOptions = [
            'once' => 'Unique',
            'monthly' => 'Mensuelle',
            'yearly' => 'Annuelle',
        ];
        
        return view('bills.edit', compact('bill', 'categories', 'recurrenceOptions'));
    }
    
    /**
     * Met à jour une facture.
     */
    public function update(Request $request, Bill $bill): RedirectResponse
    {
        $this->authorize('update', $bill);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'frequency' => 'required|in:once,monthly,yearly',
            'payment_method' => 'nullable|string',
            'auto_pay' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);
        
        $dueDate = Carbon::parse($request->due_date);
        
        // Déterminer le statut en fonction de la date d'échéance
        $status = $bill->status;
        if ($bill->status !== 'paid') {
            $status = $dueDate->isPast() ? 'overdue' : 'pending';
        }
        
        $bill->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'category_id' => $request->category_id,
            'frequency' => $request->frequency,
            'payment_method' => $request->payment_method,
            'auto_pay' => $request->auto_pay ? true : false,
            'status' => $status,
            'is_recurring' => $request->frequency !== 'once',
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('bills.index')
            ->with('success', 'Facture mise à jour avec succès.');
    }
    
    /**
     * Supprime une facture.
     */
    public function destroy(Bill $bill): RedirectResponse
    {
        $this->authorize('delete', $bill);
        
        $bill->delete();
        
        return redirect()->route('bills.index')
            ->with('success', 'Facture supprimée avec succès.');
    }
    
    /**
     * Marque une facture comme payée.
     */
    public function markAsPaid(Request $request, Bill $bill): RedirectResponse
    {
        $this->authorize('update', $bill);
        
        $request->validate([
            'paid_date' => 'nullable|date',
            'create_transaction' => 'nullable|boolean',
        ]);
        
        $paidDate = $request->paid_date ?? Carbon::now();
        
        $bill->update([
            'status' => 'paid',
            'paid_date' => $paidDate,
        ]);
        
        // Créer une transaction pour ce paiement si demandé
        if ($request->create_transaction) {
            $user = Auth::user();
            
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $bill->category_id,
                'description' => 'Paiement: ' . $bill->name,
                'amount' => $bill->amount,
                'type' => 'expense',
                'transaction_date' => $paidDate,
                'notes' => 'Paiement automatique depuis la section factures',
            ]);
        }
        
        // Si la facture est récurrente, créer la prochaine occurrence
        if ($bill->is_recurring && $bill->frequency !== 'once') {
            $nextDueDate = null;
            
            switch ($bill->frequency) {
                case 'monthly':
                    $nextDueDate = Carbon::parse($bill->due_date)->addMonth();
                    break;
                case 'yearly':
                    $nextDueDate = Carbon::parse($bill->due_date)->addYear();
                    break;
            }
            
            if ($nextDueDate) {
                Bill::create([
                    'user_id' => $bill->user_id,
                    'name' => $bill->name,
                    'amount' => $bill->amount,
                    'due_date' => $nextDueDate,
                    'category_id' => $bill->category_id,
                    'frequency' => $bill->frequency,
                    'payment_method' => $bill->payment_method,
                    'auto_pay' => $bill->auto_pay,
                    'status' => 'pending',
                    'is_recurring' => true,
                    'notes' => $bill->notes,
                ]);
            }
        }
        
        return redirect()->route('bills.index')
            ->with('success', 'Facture marquée comme payée avec succès.');
    }
    
    /**
     * Affiche le calendrier des factures.
     */
    public function calendar(): View
    {
        $user = Auth::user();
        
        // Récupérer toutes les factures non payées
        $bills = Bill::where('user_id', $user->id)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->get();
        
        // Préparer les données pour le calendrier
        $calendarEvents = $bills->map(function ($bill) {
            $dueDate = Carbon::parse($bill->due_date);
            $isPastDue = $dueDate->isPast();
            
            return [
                'id' => $bill->id,
                'title' => $bill->name,
                'start' => $bill->due_date,
                'url' => route('bills.show', $bill->id),
                'backgroundColor' => $bill->status === 'overdue' ? '#DC2626' : '#3B82F6',
                'borderColor' => $bill->status === 'overdue' ? '#B91C1C' : '#2563EB',
                'amount' => $bill->amount,
                'isPastDue' => $isPastDue,
            ];
        });
        
        return view('bills.calendar', compact('calendarEvents'));
    }
} 