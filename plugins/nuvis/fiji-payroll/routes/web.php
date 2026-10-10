<?php

use Illuminate\Support\Facades\Route;
use Nuvis\FijiPayroll\Models\SalarySlip;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('fiji-payroll/payslip/{id}', function ($id) {
        $slip = SalarySlip::with(['payrollRun', 'employee'])->findOrFail($id);

        return view('fiji-payroll::salary-slip', ['slip' => $slip]);
    })->name('fiji-payroll.payslip');
});
