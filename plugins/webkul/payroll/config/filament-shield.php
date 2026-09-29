<?php

use Webkul\Payroll\Models\ContributionRegister;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\PayrollPeriod;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Models\PayrollStructure;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Models\SalaryRule;

return [
    'resources' => [
        'manage' => [
            PayrollStructure::class,
            SalaryRule::class,
            EmployeeContract::class,
            PayrollPeriod::class,
            PayrollRun::class,
            Payslip::class,
            ContributionRegister::class,
        ],
        'exclude' => [],
    ],
    'pages' => [
        'exclude' => [],
    ],
    'widgets' => [
        'exclude' => [],
    ],
];
