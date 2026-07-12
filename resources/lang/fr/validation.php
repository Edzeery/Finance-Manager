<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'min' => [
        'numeric' => 'Le champ :attribute doit être d\'au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'budget_exceeded' => 'Cela dépasse le budget. Alloué : :allocated, dépensé : :spent, restant : :remaining.',
    'password' => [
        'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
];
