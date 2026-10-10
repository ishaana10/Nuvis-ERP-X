<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiji_salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('fiji_payroll_runs')->cascadeOnDelete();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->boolean('is_resident')->default(true);
            $table->string('pay_frequency')->default('monthly');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('overtime', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('taxable_income', 12, 2)->default(0);
            $table->decimal('fnpf_employee', 12, 2)->default(0);
            $table->decimal('fnpf_employer', 12, 2)->default(0);
            $table->decimal('paye_tax', 12, 2)->default(0);
            $table->decimal('workcare_levy', 12, 2)->default(0);
            $table->decimal('training_levy', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->json('rate_snapshot')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiji_salary_slips');
    }
};
