<?php

namespace Webkul\Invoice\Filament\Clusters\Reporting\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZReportExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected array $reportData;

    protected array $filters;

    public function __construct(array $reportData, array $filters)
    {
        $this->reportData = $reportData;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Z-Report Summary';
    }

    public function headings(): array
    {
        return [
            ['Z REPORT SUMMARY'],
            ['Date Range:', ($this->filters['date_from'] ?? 'All').' to '.($this->filters['date_to'] ?? 'All')],
            ['Generated At:', now()->format('Y-m-d H:i:s')],
            [''],
            ['Category / Description', 'Amount ($)'],
        ];
    }

    public function array(): array
    {
        $summary = $this->reportData['summary'];
        $taxBreakdown = $this->reportData['taxBreakdown'];
        $payments = $this->reportData['payments'];

        $rows = [
            ['--- SALES SUMMARY ---', ''],
            ['Gross Sales (Untaxed)', number_format($summary['gross_sales_untaxed'], 2)],
            ['Gross Tax Collected', number_format($summary['gross_sales_tax'], 2)],
            ['Gross Sales Total', number_format($summary['gross_sales_total'], 2)],
            ['Total Refunds / Returns', '-'.number_format($summary['refunds_total'], 2)],
            ['NET SALES TOTAL', number_format($summary['net_sales_total'], 2)],
            ['', ''],
            ['--- TAX BREAKDOWN ---', ''],
        ];

        foreach ($taxBreakdown as $tax) {
            $rows[] = [$tax['name'], number_format($tax['amount'], 2)];
        }

        $rows[] = ['Total Net Tax', number_format($summary['net_sales_tax'], 2)];
        $rows[] = ['', ''];
        $rows[] = ['--- PAYMENTS COLLECTED ---', ''];

        foreach ($payments as $pay) {
            $rows[] = [$pay['journal'], number_format($pay['amount'], 2)];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }
}
