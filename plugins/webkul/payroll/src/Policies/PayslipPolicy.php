<?php

namespace Webkul\Payroll\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Payroll\Models\Payslip;
use Webkul\Security\Models\User;

class PayslipPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function view(User $user, Payslip $model): bool
    {
        return $user->can('view_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function update(User $user, Payslip $model): bool
    {
        return $user->can('update_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Payslip $model): bool
    {
        return $user->can('delete_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function restore(User $user, Payslip $model): bool
    {
        return $user->can('restore_payroll_payslip') || $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Payslip $model): bool
    {
        return $user->can('force_delete_payroll_payslip') || $user->hasRole('super_admin');
    }
}
