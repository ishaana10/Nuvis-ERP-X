<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate([
            'name'       => 'page_support_manage_multi_tenancy',
            'guard_name' => 'web',
        ]);

        $superAdminRole = Role::where('name', 'super_admin')->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        Permission::where('name', 'page_support_manage_multi_tenancy')->delete();
    }
};
