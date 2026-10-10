<?php

$autoload = __DIR__.'/../../../../vendor/autoload.php';
if (! file_exists($autoload)) {
    $autoload = __DIR__.'/../vendor/autoload.php';
}
require_once $autoload;

$bootstrap = __DIR__.'/../../../../bootstrap/app.php';
if (file_exists($bootstrap)) {
    $app = require_once $bootstrap;
    $app->make(Kernel::class)->bootstrap();
}

use Illuminate\Contracts\Console\Kernel;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Services\PayrollCalculator;

$calculator = app(PayrollCalculator::class);

$result = $calculator->calculate([
    'basic'       => 3000,
    'overtime'    => 250,
    'allowances'  => 150,
    'is_resident' => true,
    'frequency'   => PayFrequency::Monthly,
]);

echo "--- FIJI PAYROLL CALCULATION DEMO ---\n";
echo 'Gross Pay:           $'.number_format($result['gross'], 2)."\n";
echo 'FNPF Employee (8%):  $'.number_format($result['fnpf_employee'], 2)."\n";
echo 'FNPF Employer (8%):  $'.number_format($result['fnpf_employer'], 2)."\n";
echo 'Taxable Income:      $'.number_format($result['taxable_income'], 2)."\n";
echo 'PAYE Tax:            $'.number_format($result['paye'], 2)."\n";
echo 'Net Pay:             $'.number_format($result['net_pay'], 2)."\n";
echo 'WorkCare Levy (1%):  $'.number_format($result['workcare_levy'], 2)."\n";
echo 'Training Levy (1%):  $'.number_format($result['training_levy'], 2)."\n";
echo 'Total Employer Cost: $'.number_format($result['total_employer_cost'], 2)."\n";
