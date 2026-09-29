<?php

namespace Webkul\Payroll\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Payroll\Enums\AmountType;
use Webkul\Payroll\Enums\RuleCategory;
use Webkul\Payroll\Models\ContributionRegister;
use Webkul\Payroll\Models\PayrollStructure;
use Webkul\Payroll\Models\SalaryRule;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $taxRegister = ContributionRegister::firstOrCreate(
            ['name' => 'Tax Authority'],
            ['note' => 'National Tax & Income Authority']
        );

        $socialRegister = ContributionRegister::firstOrCreate(
            ['name' => 'Social Security Fund'],
            ['note' => 'Employee & Employer Social Security Fund']
        );

        $structure = PayrollStructure::firstOrCreate(
            ['code' => 'BASE_REGULAR'],
            [
                'name'      => 'Regular Monthly Salary Structure',
                'is_active' => true,
                'notes'     => 'Standard monthly structure with basic, allowances, tax, and social security.',
            ]
        );

        // Basic Salary Rule
        SalaryRule::firstOrCreate(
            ['structure_id' => $structure->id, 'code' => 'BASIC'],
            [
                'name'                   => 'Basic Salary',
                'category'               => RuleCategory::BASIC,
                'sequence'               => 1,
                'amount_type'            => AmountType::PERCENTAGE,
                'amount_percentage'      => 100.0,
                'amount_percentage_base' => 'wage',
                'appears_on_payslip'     => true,
                'is_employer'            => false,
            ]
        );

        // Housing Allowance
        SalaryRule::firstOrCreate(
            ['structure_id' => $structure->id, 'code' => 'HOU_ALLOW'],
            [
                'name'                   => 'Housing Allowance',
                'category'               => RuleCategory::ALLOWANCE,
                'sequence'               => 10,
                'amount_type'            => AmountType::PERCENTAGE,
                'amount_percentage'      => 15.0,
                'amount_percentage_base' => 'basic',
                'appears_on_payslip'     => true,
                'is_employer'            => false,
            ]
        );

        // Income Tax Deduction
        SalaryRule::firstOrCreate(
            ['structure_id' => $structure->id, 'code' => 'TAX_INC'],
            [
                'contribution_register_id' => $taxRegister->id,
                'name'                     => 'Income Tax (PAYE)',
                'category'                 => RuleCategory::TAX,
                'sequence'                 => 100,
                'amount_type'              => AmountType::PERCENTAGE,
                'amount_percentage'        => 10.0,
                'amount_percentage_base'   => 'gross',
                'appears_on_payslip'       => true,
                'is_employer'              => false,
            ]
        );

        // Employee Social Contribution
        SalaryRule::firstOrCreate(
            ['structure_id' => $structure->id, 'code' => 'SOC_EE'],
            [
                'contribution_register_id' => $socialRegister->id,
                'name'                     => 'Employee Social Security',
                'category'                 => RuleCategory::SOCIAL,
                'sequence'                 => 105,
                'amount_type'              => AmountType::PERCENTAGE,
                'amount_percentage'        => 5.0,
                'amount_percentage_base'   => 'gross',
                'appears_on_payslip'       => true,
                'is_employer'              => false,
            ]
        );

        // Employer Social Contribution
        SalaryRule::firstOrCreate(
            ['structure_id' => $structure->id, 'code' => 'SOC_ER'],
            [
                'contribution_register_id' => $socialRegister->id,
                'name'                     => 'Employer Social Security Contribution',
                'category'                 => RuleCategory::SOCIAL,
                'sequence'                 => 200,
                'amount_type'              => AmountType::PERCENTAGE,
                'amount_percentage'        => 8.0,
                'amount_percentage_base'   => 'gross',
                'appears_on_payslip'       => true,
                'is_employer'              => true,
            ]
        );
    }
}
