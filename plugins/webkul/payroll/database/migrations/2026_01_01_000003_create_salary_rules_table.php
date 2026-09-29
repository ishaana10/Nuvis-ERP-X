<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payroll_salary_rules')) {
            Schema::create('payroll_salary_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('structure_id')->constrained('payroll_structures')->cascadeOnDelete();
                $table->foreignId('contribution_register_id')->nullable()->constrained('payroll_contribution_registers')->nullOnDelete();
                $table->string('name');
                $table->string('code');
                $table->string('category')->default('basic'); // basic, allowance, gross, deduction, tax, social, net
                $table->integer('sequence')->default(10);
                $table->string('amount_type')->default('fixed'); // fixed, percentage, formula
                $table->decimal('amount_fix', 15, 4)->default(0.0000);
                $table->decimal('amount_percentage', 8, 4)->default(0.0000);
                $table->string('amount_percentage_base')->default('basic'); // basic, gross, wage
                $table->text('amount_python_compute')->nullable();
                $table->string('condition_select')->default('none'); // none, python, range
                $table->decimal('condition_range_min', 15, 4)->nullable();
                $table->decimal('condition_range_max', 15, 4)->nullable();
                $table->text('condition_python')->nullable();
                $table->boolean('appears_on_payslip')->default(true);
                $table->boolean('is_employer')->default(false);
                $table->boolean('is_active')->default(true);
                $table->foreignId('account_id')->nullable()->constrained('accounts_accounts')->nullOnDelete();
                $table->foreignId('employer_account_id')->nullable()->constrained('accounts_accounts')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_salary_rules');
    }
};
