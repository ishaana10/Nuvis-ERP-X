<?php

namespace Webkul\Payroll\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Payroll\Models\PayrollPeriod;
use Webkul\Security\Models\User;

class PayrollPeriodPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payroll_period') || $user->hasRole('super_admin');
    }

    public function view(User $user, PayrollPeriod $model): bool
    {
        return $user->can('view_payroll_period') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_payroll_period') || $user->hasRole('super_admin');
    }

    public function update(User $user, PayrollPeriod $model): bool
    {
        return $user->can('update_payroll_period') || $user->hasRole('super_admin');
    }

    public function delete(User $user, PayrollPeriod $model): bool
    {
        return $user->can('delete_payroll_period') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_payroll_period') || $user->hasRole('super_admin');
    }

    public function restore(User $user, PayrollPeriod $model): bool
    {
        return $user->can('restore_payroll_period') || $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, PayrollPeriod $model): bool
    {
        return $user->can('force_delete_payroll_period') || $user->hasRole('super_admin');
    }
}
