<?php

return [
    'breadcrumb' => 'Multi-Tenancy Options',
    'title'      => 'Multi-Tenancy Options',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Multi-Tenancy Options',
    ],

    'form' => [
        'sections' => [
            'tenancy' => [
                'title'       => 'Multi-Tenant Configuration',
                'description' => 'Configure multi-tenancy rules, active tenant switching behavior, and header visibility across the platform.',
            ],
            'isolation' => [
                'title'       => 'Data Isolation & Tenant Management',
                'description' => 'Control strict company isolation rules and tenant creation capabilities.',
            ],
        ],
        'fields' => [
            'enable-multi-tenancy'                      => 'Enable Multi-Tenancy Mode',
            'enable-multi-tenancy-helper'               => 'Allows the platform to isolate data and operations across multiple companies / tenants.',
            'tenant-switch-mode'                        => 'Tenant Switching Mode',
            'tenant-switch-mode-helper'                 => 'Choose whether users can switch between multiple active tenants simultaneously or focus on a single tenant space at a time.',
            'show-tenant-switcher'                      => 'Show Tenant Switcher in Header',
            'show-tenant-switcher-helper'               => 'Display the quick company switcher dropdown in the top global navigation bar.',
            'strict-tenant-isolation'                   => 'Strict Data Isolation',
            'strict-tenant-isolation-helper'            => 'Strictly restrict data visibility to the current active tenant only, hiding shared unassigned records.',
            'allow-self-service-tenant-creation'        => 'Allow Self-Service Tenant Creation',
            'allow-self-service-tenant-creation-helper' => 'Permit company administrators to create new organization spaces / branches.',
            'default-tenant'                            => 'Default Tenant',
            'default-tenant-helper'                     => 'Fallback default tenant company assigned for newly onboarded accounts.',
        ],
        'options' => [
            'switch-mode-multi'  => 'Multi Active Tenants (Combined View)',
            'switch-mode-single' => 'Single Active Tenant (Isolated Focus)',
        ],
    ],
];
