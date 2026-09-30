<?php

return [
    'navigation' => [
        'title' => 'Reporting',
    ],

    'pages' => [
        'invoice-report' => [
            'navigation' => [
                'title' => 'Invoice Reports',
                'group' => 'Reporting',
            ],

            'actions' => [
                'export-excel' => 'Export Excel',
                'export-pdf'   => 'Export PDF',
            ],

            'filters' => [
                'preset' => 'Report Preset',
                'preset-options' => [
                    'all'        => 'All Invoices',
                    'unpaid'     => 'Unpaid Invoices',
                    'short_paid' => 'Short Paid Invoices',
                    'overdue'    => 'Overdue Invoices',
                    'customer'   => 'By Customer',
                ],
                'date-from'          => 'Invoice Date From',
                'date-to'            => 'Invoice Date To',
                'due-date-from'      => 'Due Date From',
                'due-date-to'        => 'Due Date To',
                'customers'          => 'Customers / Partners',
                'payment-state'      => 'Payment State',
                'move-state'         => 'Invoice Status',
                'move-type'          => 'Document Type',
                'group-by'           => 'Group By',
                'group-by-options'   => [
                    'none'          => 'None (Detailed List)',
                    'customer'      => 'Customer',
                    'payment_state' => 'Payment State',
                    'status'        => 'Invoice Status',
                ],
                'options' => [
                    'posted' => 'Posted',
                    'draft'  => 'Draft',
                    'cancel' => 'Cancelled',
                ],
            ],

            'stats' => [
                'total-invoices'   => 'Total Invoices',
                'total-amount'     => 'Total Invoiced',
                'total-paid'       => 'Total Paid',
                'total-outstanding' => 'Total Outstanding',
            ],

            'table' => [
                'number'            => 'Number',
                'customer'          => 'Customer',
                'invoice-date'      => 'Invoice Date',
                'due-date'          => 'Due Date',
                'type'              => 'Type',
                'total-amount'      => 'Total Amount',
                'paid-amount'       => 'Paid Amount',
                'residual-amount'   => 'Outstanding Balance',
                'payment-state'     => 'Payment State',
                'status'            => 'Status',
            ],
        ],
    ],
];
