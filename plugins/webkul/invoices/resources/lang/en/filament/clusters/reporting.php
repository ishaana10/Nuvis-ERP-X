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
                'preset'          => 'Report Preset',
                'preset-options'  => [
                    'all'        => 'All Invoices',
                    'unpaid'     => 'Unpaid Invoices',
                    'short_paid' => 'Short Paid Invoices',
                    'overdue'    => 'Overdue Invoices',
                    'customer'   => 'Report By Customer',
                ],
                'group-by'         => 'Group By',
                'group-by-options' => [
                    'none'          => 'None',
                    'customer'      => 'Customer',
                    'payment_state' => 'Payment State',
                    'status'        => 'Status',
                ],
                'date-from'       => 'Invoice Date From',
                'date-to'         => 'Invoice Date To',
                'due-date-from'   => 'Due Date From',
                'due-date-to'     => 'Due Date To',
                'customers'       => 'Customers / Partners',
                'payment-state'   => 'Payment State',
                'move-state'      => 'Invoice Status',
                'move-type'       => 'Document Type',
            ],

            'stats' => [
                'total-invoices'    => 'Total Invoices',
                'total-amount'      => 'Total Invoiced',
                'total-paid'        => 'Total Paid',
                'total-outstanding' => 'Total Outstanding',
            ],

            'table' => [
                'number'          => 'Invoice #',
                'customer'        => 'Customer',
                'invoice-date'    => 'Invoice Date',
                'due-date'        => 'Due Date',
                'type'            => 'Document Type',
                'total-amount'    => 'Total',
                'paid-amount'     => 'Paid',
                'residual-amount' => 'Amount Due',
                'payment-state'   => 'Payment Status',
                'status'          => 'Status',
                'columns' => [
                    'number'         => 'Invoice #',
                    'date'           => 'Invoice Date',
                    'due-date'       => 'Due Date',
                    'customer'       => 'Customer',
                    'payment-status' => 'Payment Status',
                    'status'         => 'Status',
                    'total'          => 'Total',
                    'amount-paid'    => 'Paid',
                    'amount-due'     => 'Amount Due',
                ],
                'group' => [
                    'total-label' => 'Subtotal',
                    'count-label' => 'invoices',
                ],
                'empty' => 'No invoices match the selected filter criteria.',
            ],
        ],

        'z-report' => [
            'navigation' => [
                'title' => 'Z Report',
                'group' => 'Reporting',
            ],

            'actions' => [
                'export-excel' => 'Export Excel',
                'export-pdf'   => 'Export PDF',
            ],

            'filters' => [
                'date-from'    => 'Date From',
                'date-to'      => 'Date To',
                'journal'      => 'Journal',
                'all-journals' => 'All Journals',
            ],

            'summary' => [
                'gross-sales'         => 'Gross Sales',
                'invoices'            => 'invoices',
                'refunds-returns'     => 'Refunds / Returns',
                'refunds'             => 'credit notes',
                'net-sales'           => 'Net Sales',
                'untaxed'             => 'Untaxed',
                'gross-sales-untaxed' => 'Gross Sales (Untaxed)',
                'gross-sales-tax'     => 'Gross Tax Collected',
                'gross-sales-total'   => 'Gross Sales Total',
                'total-refunds'       => 'Total Refunds / Credit Notes',
                'net-sales-total'     => 'Net Sales Total',
                'total-tax'           => 'Total Net Tax',
            ],

            'sections' => [
                'sales-summary'      => 'Sales Summary',
                'tax-summary'        => 'Tax Breakdown',
                'payments-collected' => 'Payments Collected by Journal',
            ],

            'tables' => [
                'journal' => 'Journal',
                'amount'  => 'Amount',
            ],

            'messages' => [
                'no-tax'      => 'No tax records for the selected period.',
                'no-payments' => 'No payments recorded for the selected period.',
            ],
        ],
    ],
];
