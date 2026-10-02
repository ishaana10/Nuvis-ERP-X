<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;

class RevenuePerformanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'lg' => 2,
    ];

    public function getHeading(): ?string
    {
        return 'Revenue Performance vs Baseline Target';
    }

    protected function getData(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $actuals = [];
        $targets = [];

        foreach (range(1, 12) as $m) {
            $total = Move::where('move_type', MoveType::OUT_INVOICE->value)
                ->where('state', 'posted')
                ->whereMonth('date', $m)
                ->sum('amount_total');

            $actuals[] = round($total, 2);
            $targets[] = 10000;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Actual Revenue ($)',
                    'data' => $actuals,
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                ],
                [
                    'label' => 'Target Revenue ($)',
                    'data' => $targets,
                    'borderColor' => '#9ca3af',
                    'borderDash' => [5, 5],
                    'backgroundColor' => 'transparent',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
