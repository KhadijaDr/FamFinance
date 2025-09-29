<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class BillTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste si un utilisateur authentifié peut créer une facture.
     */
    public function test_authenticated_user_can_create_bill()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $billData = [
            'name' => 'Facture de test',
            'description' => 'Ceci est une facture de test.',
            'amount' => 50.00,
            'due_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'category_id' => $category->id,
            'frequency' => 'monthly',
            'payment_method' => 'Carte bancaire',
            'auto_pay' => true,
            'notes' => 'Notes de test.',
        ];

        $response = $this->post(route('bills.store'), $billData);

        // Vérifier la redirection
        $response->assertRedirect(route('bills.index'));

        // Vérifier que la facture a été créée dans la base de données
        $this->assertDatabaseHas('bills', [
            'user_id' => $user->id,
            'name' => 'Facture de test',
            'amount' => 50.00,
            'due_date' => $billData['due_date'],
            'category_id' => $category->id,
            'frequency' => 'monthly',
            'payment_method' => 'Carte bancaire',
            'auto_pay' => true,
            'notes' => 'Notes de test.',
            'status' => 'pending', // Doit être en attente car la date d'échéance est future
            'is_recurring' => true,
        ]);

        // Vérifier le message de succès dans la session
        $response->assertSessionHas('success', 'Facture ajoutée avec succès.');
    }

    // Vous pouvez ajouter d'autres tests ici, par exemple pour la validation
} 