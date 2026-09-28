<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vms_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('tin')->nullable();
            $table->string('mrc')->nullable();
            $table->string('pac')->nullable();
            $table->string('pos_number')->default('POS-001/1.0');
            $table->string('environment')->default('sandbox');
            $table->string('sdc_type')->default('V-SDC');
            $table->string('api_url')->default('https://tap.sandbox.vms.frcs.org.fj');
            $table->text('pfx_certificate')->nullable();
            $table->string('certificate_password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vms_tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('name');
            $table->decimal('rate', 8, 4);
            $table->timestamp('valid_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vms_fiscal_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('account_move_id')->nullable()->constrained('accounts_account_moves')->nullOnDelete();
            $table->string('invoice_type')->default('Normal');
            $table->string('transaction_type')->default('Sale');
            $table->string('sdc_invoice_no')->nullable()->index();
            $table->timestamp('sdc_time')->nullable();
            $table->string('invoice_counter')->nullable();
            $table->string('requested_by')->nullable();
            $table->string('signed_by')->nullable();
            $table->string('cashier')->nullable();
            $table->string('buyer_tin')->nullable();
            $table->string('buyer_cost_center')->nullable();
            $table->string('ref_sdc_no')->nullable()->index();
            $table->timestamp('ref_time')->nullable();
            $table->text('verification_url')->nullable();
            $table->text('qr_code_data')->nullable();
            $table->text('encrypted_signature')->nullable();
            $table->decimal('total_amount', 16, 4)->default(0);
            $table->decimal('total_tax', 16, 4)->default(0);
            $table->string('payment_method')->default('Cash');
            $table->string('status')->default('draft');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('vms_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('secure_component_uid')->nullable();
            $table->integer('ordinal_number')->default(1);
            $table->string('log_type')->default('info');
            $table->text('message')->nullable();
            $table->json('package_data')->nullable();
            $table->string('status')->default('pending');
            $table->text('poa_signature')->nullable();
            $table->string('error_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vms_audit_logs');
        Schema::dropIfExists('vms_fiscal_invoices');
        Schema::dropIfExists('vms_tax_rates');
        Schema::dropIfExists('vms_settings');
    }
};
