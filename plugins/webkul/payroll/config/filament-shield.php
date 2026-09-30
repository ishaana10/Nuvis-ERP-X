<?php

use Webkul\Payroll\Models\ContributionRegister;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\PayrollPeriod;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Models\PayrollStructure;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Models\SalaryRule;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];

return [
    'resources' => [
        'manage' => [
            PayrollStructure::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            SalaryRule::class            => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            EmployeeContract::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PayrollPeriod::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PayrollRun::class            => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            Payslip::class               => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ContributionRegister::class  => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [],
    ],
];
