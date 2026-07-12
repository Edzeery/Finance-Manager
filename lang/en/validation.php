<?php

return [
    'required' => 'The :attribute field is required.',
    'numeric' => 'The :attribute must be a number.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'date' => 'The :attribute is not a valid date.',
    'budget_exceeded' => 'This exceeds the budget. Allocated: :allocated, spent: :spent, remaining: :remaining.',
    'password' => [
        'min' => 'The :attribute must be at least :min characters.',
    ],
];
