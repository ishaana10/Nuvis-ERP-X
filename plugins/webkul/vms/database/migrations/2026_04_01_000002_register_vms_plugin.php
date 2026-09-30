<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('plugins')) {
            DB::table('plugins')->updateOrInsert(
                ['name' => 'vms'],
                [
                    'author'         => 'Nuvis ERP X',
                    'summary'        => 'VAT Monitoring System (VMS) EFD and Fiscalization Module for FRCS Compliance',
                    'description'    => 'VAT Monitoring System (VMS) EFD and Fiscalization Module for FRCS Compliance',
                    'latest_version' => '1.0.0',
                    'license'        => 'MIT',
                    'is_active'      => true,
                    'is_installed'   => true,
                    'updated_at'     => now(),
                    'created_at'     => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('plugins')) {
            DB::table('plugins')->where('name', 'vms')->delete();
        }
    }
};
