<?php

namespace App\Policies;

use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinancialGoalPolicy
{
    /**
     * Determine whether the user can view the financial goal.
     */
    public function view(User $user, FinancialGoal $financialGoal): bool
    {
        return $user->id === $financialGoal->user_id;
    }

    /**
     * Determine whether the user can create financial goals.
     */
    public function create(User $user): bool
    {
        return true; // Everyone authenticated can create goals
    }

    /**
     * Determine whether the user can update the financial goal.
     */
    public function update(User $user, FinancialGoal $financialGoal): bool
    {
        return $user->id === $financialGoal->user_id;
    }

    /**
     * Determine whether the user can delete the financial goal.
     */
    public function delete(User $user, FinancialGoal $financialGoal): bool
    {
        return $user->id === $financialGoal->user_id;
    }

    /**
     * Determine whether the user can restore the financial goal.
     */
    public function restore(User $user, FinancialGoal $financialGoal): bool
    {
        return $user->id === $financialGoal->user_id;
    }

    /**
     * Determine whether the user can permanently delete the financial goal.
     */
    public function forceDelete(User $user, FinancialGoal $financialGoal): bool
    {
        return $user->id === $financialGoal->user_id;
    }
} 