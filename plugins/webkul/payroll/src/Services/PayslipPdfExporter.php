<?php

namespace Webkul\Payroll\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Webkul\Payroll\Models\Payslip;

class PayslipPdfExporter
{
    /**
     * Download or generate PDF response for a payslip.
     */
    public function download(Payslip $payslip): Response
    {
        $payslip->loadMissing(['employee.department', 'contract', 'company', 'lines']);

        $pdf = Pdf::loadView('payroll::pdf.payslip', [
            'payslip' => $payslip,
        ]);

        $fileName = 'Payslip_'.($payslip->number ?: $payslip->id).'.pdf';

        return $pdf->download($fileName);
    }
}
