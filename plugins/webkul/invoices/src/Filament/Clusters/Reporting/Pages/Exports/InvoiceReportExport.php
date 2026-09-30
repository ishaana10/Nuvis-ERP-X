<?php

namespace Webkul\Invoice\Filament\Clusters\Reporting\Pages\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceReportExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected array $reportData;

    protected array $filters;

    protected array $rowMetadata = [];

    public function __construct(array $reportData, array $filters = [])
    {
        $this->reportData = $reportData;
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            ['Invoice Report - '.now()->format('Y-m-d H:i')],
            [],
            [
                'Invoice Number',
                'Customer',
                'Invoice Date',
                'Due Date',
                'Document Type',
                'Total Amount',
                'Paid Amount',
                'Outstanding Balance',
                'Payment State',
                'Status',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $rowIndex = 4;

        $invoices = $this->reportData['invoices'];
        $groupedData = $this->reportData['groupedData'];
        $groupBy = $this->reportData['groupBy'];
        $stats = $this->reportData['stats'];

        if ($groupBy !== 'none' && ! empty($groupedData)) {
            foreach ($groupedData as $groupName => $groupInvoices) {
                $groupTotal = $groupInvoices->sum('amount_total');
                $groupResidual = $groupInvoices->sum('amount_residual');
                $groupPaid = $groupTotal - $groupResidual;

                $rows[] = [
                    $groupName.' ('.$groupInvoices->count().' invoices)',
                    '',
                    '',
                    '',
                    '',
                    $groupTotal,
                    $groupPaid,
                    $groupResidual,
                    '',
                    '',
                ];
                $this->rowMetadata[$rowIndex++] = 'group_header';

                foreach ($groupInvoices as $invoice) {
                    $paidAmount = $invoice->amount_total - $invoice->amount_residual;

                    $rows[] = [
                        $invoice->name ?: '#'.$invoice->id,
                        $invoice->partner?->name ?? 'N/A',
                        $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->format('Y-m-d') : 'N/A',
                        $invoice->invoice_date_due ? Carbon::parse($invoice->invoice_date_due)->format('Y-m-d') : 'N/A',
                        $invoice->move_type?->getLabel() ?? $invoice->move_type,
                        $invoice->amount_total,
                        $paidAmount,
                        $invoice->amount_residual,
                        $invoice->payment_state?->getLabel() ?? 'N/A',
                        $invoice->state?->getLabel() ?? 'N/A',
                    ];
                    $this->rowMetadata[$rowIndex++] = 'line';
                }
            }
        } else {
            foreach ($invoices as $invoice) {
                $paidAmount = $invoice->amount_total - $invoice->amount_residual;

                $rows[] = [
                    $invoice->name ?: '#'.$invoice->id,
                    $invoice->partner?->name ?? 'N/A',
                    $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->format('Y-m-d') : 'N/A',
                    $invoice->invoice_date_due ? Carbon::parse($invoice->invoice_date_due)->format('Y-m-d') : 'N/A',
                    $invoice->move_type?->getLabel() ?? $invoice->move_type,
                    $invoice->amount_total,
                    $paidAmount,
                    $invoice->amount_residual,
                    $invoice->payment_state?->getLabel() ?? 'N/A',
                    $invoice->state?->getLabel() ?? 'N/A',
                ];
                $this->rowMetadata[$rowIndex++] = 'line';
            }
        }

        // Grand Total row
        $rows[] = [
            'Grand Total ('.$stats['total_count'].' invoices)',
            '',
            '',
            '',
            '',
            $stats['total_amount'],
            $stats['total_paid'],
            $stats['total_residual'],
            '',
            '',
        ];
        $this->rowMetadata[$rowIndex] = 'grand_total';

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 16,
            'J' => 16,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A1:J1');

        $sheet->getStyle('A3:J3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color'       => ['rgb' => '666666'],
                ],
            ],
        ]);

        $styleMap = [
            'group_header' => [
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '333333']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders'   => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
            'line' => [
                'font'      => ['size' => 10, 'color' => ['rgb' => '333333']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'grand_total' => [
                'font'    => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];

        foreach ($this->rowMetadata as $rowNum => $type) {
            if (isset($styleMap[$type])) {
                $sheet->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray($styleMap[$type]);
            }
        }

        $lastRow = count($this->rowMetadata) + 3;
        $sheet->getStyle("F4:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("F4:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
