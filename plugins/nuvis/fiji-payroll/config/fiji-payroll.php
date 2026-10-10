<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FNPF (Fiji National Provident Fund) Rates
    |--------------------------------------------------------------------------
    |
    | FNPF contributions for eligible wage bases.
    |
    */
    'fnpf' => [
        'employee_rate'  => 0.08, // 8%
        'employer_rate'  => 0.08, // 8%
        'effective_from' => '2026-08-01',
        'effective_to'   => '2027-07-31',
    ],

    /*
    |--------------------------------------------------------------------------
    | PAYE Tax Rates & Brackets (Annualized Rules)
    |--------------------------------------------------------------------------
    |
    | Resident progressive brackets and non-resident flat rate.
    |
    */
    'paye' => [
        'non_resident_rate' => 0.20, // 20%
        'resident_brackets' => [
            [
                'threshold' => 30000,
                'rate'      => 0.00,
                'base_tax'  => 0,
            ],
            [
                'threshold' => 50000,
                'rate'      => 0.18,
                'base_tax'  => 0,
            ],
            [
                'threshold' => 270000,
                'rate'      => 0.20,
                'base_tax'  => 3600,
            ],
            [
                'threshold' => INF,
                'rate'      => 0.39, // Includes SRT / top bracket approximation
                'base_tax'  => 47600,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statutory Levies
    |--------------------------------------------------------------------------
    |
    | Employer statutory levies.
    |
    */
    'levies' => [
        'workcare_rate' => 0.01, // 1%
        'training_rate' => 0.01, // 1%
    ],
];
