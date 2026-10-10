<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiji_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('pay_frequency')->default('monthly');
            $table->string('status')->default('draft');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_fnpf_employee', 12, 2)->default(0);
            $table->decimal('total_fnpf_employer', 12, 2)->default(0);
            $table->decimal('total_paye', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiji_payroll_runs');
    }
};
