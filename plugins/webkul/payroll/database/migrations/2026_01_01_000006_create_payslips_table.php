<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_payslips', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->nullable();
            $table->foreignId('employee_id')->constrained('employees_employees')->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('payroll_employee_contracts')->nullOnDelete();
            $table->foreignId('run_id')->nullable()->constrained('payroll_runs')->nullOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('payroll_periods')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->string('state')->default('draft'); // draft, confirmed, paid, cancelled
            $table->decimal('basic_wage', 15, 4)->default(0.0000);
            $table->decimal('gross_wage', 15, 4)->default(0.0000);
            $table->decimal('net_wage', 15, 4)->default(0.0000);
            $table->decimal('total_deductions', 15, 4)->default(0.0000);
            $table->decimal('total_employer_contributions', 15, 4)->default(0.0000);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payslips');
    }
};
