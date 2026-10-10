<?php

return [
    'breadcrumb' => 'Branding & Customization',
    'title'      => 'Branding & Customization',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Branding & Customization',
    ],

    'form' => [
        'sections' => [
            'identity' => [
                'title'       => 'Application Identity',
                'description' => 'Customize the application title, brand identity, and media logos across admin and portal panels.',
            ],
            'logo' => [
                'title'       => 'Logo & Favicon',
                'description' => 'Override the logos, favicon and logo height used across the admin and customer panels. Leave a field empty to keep the default.',
            ],
            'theme' => [
                'title'       => 'Theme Presets & Palette',
                'description' => 'Select a curated 1-click theme preset or fine-tune individual accent colors.',
            ],
            'typography' => [
                'title'       => 'Typography & Layout',
                'description' => 'Select default typography font style and custom footer branding text.',
            ],
            'advanced' => [
                'title'       => 'Advanced Styling',
                'description' => 'Inject custom CSS overrides to further tailor the user interface.',
            ],
        ],
        'fields' => [
            'app-name'           => 'Application Name',
            'app-name-helper'    => 'Custom title displayed in panel headers and browser page titles.',
            'theme-preset'       => 'Theme Preset',
            'theme-preset-helper'=> 'Select a color preset to automatically populate matching color schemes.',
            'font-family'        => 'Font Family',
            'font-family-helper' => 'Font family used across the panel user interface.',
            'footer-text'        => 'Footer Branding Text',
            'footer-text-helper' => 'Custom copyright line displayed at the bottom of the user interface.',
            'custom-css'         => 'Custom CSS Overrides',
            'custom-css-helper'  => 'Add custom CSS rules (e.g. .fi-logo { filter: drop-shadow(...); }) to tweak visual elements.',
            'light-logo'         => 'Light Logo',
            'light-logo-helper'  => 'Shown on light backgrounds. Replaces the default logo.',
            'dark-logo'          => 'Dark Logo',
            'dark-logo-helper'   => 'Shown when dark mode is enabled.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Browser tab icon.',
            'logo-height'        => 'Logo Height',
            'logo-height-helper' => 'A CSS height value, e.g. 2rem or 40px.',
            'primary-color'      => 'Primary Color',
            'gray-color'         => 'Gray Color',
            'danger-color'       => 'Danger Color',
            'info-color'         => 'Info Color',
            'success-color'      => 'Success Color',
            'warning-color'      => 'Warning Color',
        ],
        'presets' => [
            'nuvis_blue' => 'Nuvis Blue (Default)',
            'emerald'    => 'Modern Emerald',
            'indigo'     => 'Indigo Velvet',
            'slate'      => 'Corporate Slate',
            'amber'      => 'Sunset Amber',
            'rose'       => 'Rose Quartz',
        ],
        'fonts' => [
            'inter'               => 'Inter (Default)',
            'plus_jakarta_sans'   => 'Plus Jakarta Sans',
            'roboto'              => 'Roboto',
            'outfit'              => 'Outfit',
            'system'              => 'System UI Default',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Reset colors to default',
        ],
    ],
];
