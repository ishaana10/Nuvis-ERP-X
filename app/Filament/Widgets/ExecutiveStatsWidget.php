<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Invoice\Models\Invoice;

class ExecutiveStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Move::where('move_type', MoveType::OUT_INVOICE->value)
            ->where('state', 'posted')
            ->sum('amount_total');

        $pendingInvoices = Move::where('move_type', MoveType::OUT_INVOICE->value)
            ->where('payment_state', 'not_paid')
            ->where('state', 'posted')
            ->count();

        $invoiceIssues = Move::where('move_type', MoveType::OUT_INVOICE->value)
            ->where('payment_state', 'partial')
            ->where('state', 'posted')
            ->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Total collected revenue from posted invoices')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Invoices Pending', (string) $pendingInvoices)
                ->description('Unpaid posted invoices awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Invoice Issues', (string) $invoiceIssues)
                ->description('Short paid & partially paid invoices')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
