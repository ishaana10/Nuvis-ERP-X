<?php

namespace Webkul\Payroll\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Payroll\Models\SalaryRule;
use Webkul\Security\Models\User;

class SalaryRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function view(User $user, SalaryRule $model): bool
    {
        return $user->can('view_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function update(User $user, SalaryRule $model): bool
    {
        return $user->can('update_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function delete(User $user, SalaryRule $model): bool
    {
        return $user->can('delete_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function restore(User $user, SalaryRule $model): bool
    {
        return $user->can('restore_payroll_salary_rule') || $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, SalaryRule $model): bool
    {
        return $user->can('force_delete_payroll_salary_rule') || $user->hasRole('super_admin');
    }
}
