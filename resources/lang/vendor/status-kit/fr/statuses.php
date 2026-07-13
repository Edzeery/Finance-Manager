<?php

return [
    'payment' => [
        'paid' => 'Payé', 'pending' => 'En attente', 'failed' => 'Échoué',
        'refunded' => 'Remboursé', 'canceled' => 'Annulé',
    ],
    'subscription' => [
        'active' => 'Actif', 'pending' => 'En attente', 'expired' => 'Expiré',
        'canceled' => 'Annulé', 'suspended' => 'Suspendu',
    ],
    'user' => [
        'active' => 'Actif', 'pending' => 'En attente', 'suspended' => 'Suspendu',
        'banned' => 'Banni', 'email_unverified' => 'Email non vérifié',
    ],
    'stores' => [
        'active' => 'Actif', 'pending' => 'En révision', 'suspended' => 'Suspendu',
        'closed' => 'Fermé', 'draft' => 'Brouillon', 'blocked' => 'Bloqué',
        'approved' => 'Approuvé', 'rejected' => 'Rejeté',
    ],
    'order' => [
        'pending' => 'En attente', 'processing' => 'En traitement', 'completed' => 'Terminé',
        'canceled' => 'Annulé', 'refunded' => 'Remboursé',
    ],
    'product' => [
        'active' => 'Disponible', 'inactive' => 'Inactif', 'out_of_stock' => 'Rupture de stock',
        'discontinued' => 'Discontinué',
    ],
    'role' => [
        'super_admin' => 'Super Administrateur', 'admin' => 'Administrateur', 'support_agent' => 'Agent Support',
        'tech_support' => 'Support Technique', 'merchant' => 'Marchand', 'user' => 'Utilisateur',
        'owner' => 'Propriétaire', 'manager' => 'Manager', 'staff' => 'Personnel',
    ],
    'general' => [
        'info' => 'Info', 'success' => 'Succès', 'warning' => 'Avertissement',
        'danger' => 'Danger', 'gray' => 'Non spécifié',
    ],
];
