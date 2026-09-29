<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_payslip_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payslip_id')->constrained('payroll_payslips')->cascadeOnDelete();
            $table->foreignId('salary_rule_id')->nullable()->constrained('payroll_salary_rules')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('category')->default('basic');
            $table->integer('sequence')->default(10);
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->decimal('rate', 8, 4)->default(100.0000);
            $table->decimal('amount', 15, 4)->default(0.0000);
            $table->decimal('total', 15, 4)->default(0.0000);
            $table->boolean('is_employer')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payslip_lines');
    }
};
