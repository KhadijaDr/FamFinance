return [
    // ... existing code ...
    
    'attributes' => [
        'name' => 'nom',
        'target_amount' => 'montant cible',
        'current_amount' => 'montant actuel',
        'start_date' => 'date de début',
        'target_date' => 'date cible',
        'priority' => 'priorité',
        'color' => 'couleur',
        'icon' => 'icône',
        'notes' => 'notes',
    ],
    
    'custom' => [
        'target_date' => [
            'after' => 'La date cible doit être postérieure à la date de début.',
        ],
    ],
]; 