<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;

class MedicationPolicy
{
    /**
     * Determina se o usuário pode atualizar o medicamento.
     */
    public function update(User $user, Medication $medication): bool
    {
        // Retorna TRUE apenas se o medicamento pertencer ao usuário logado
        return (int)$user->id === (int)$medication->user_id;
    }
}