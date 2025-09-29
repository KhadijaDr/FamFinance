<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view the transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * Determine whether the user can update the transaction.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * Determine whether the user can delete the transaction.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }
} 