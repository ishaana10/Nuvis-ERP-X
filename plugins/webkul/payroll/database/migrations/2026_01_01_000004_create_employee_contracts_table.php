<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('employee_id')->constrained('employees_employees')->cascadeOnDelete();
            $table->foreignId('structure_id')->nullable()->constrained('payroll_structures')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained('accounts_journals')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('wage', 15, 4)->default(0.0000);
            $table->string('schedule_pay')->default('monthly'); // monthly, weekly, bi-weekly
            $table->string('state')->default('open'); // draft, open, close, cancel
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_employee_contracts');
    }
};
