<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Models\Move;

class FrictionDriversWidget extends Widget
{
    protected string $view = 'filament.widgets.friction-drivers-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $overdue = Move::where('type', 'out_invoice')
            ->where('state', 'posted')
            ->where('payment_state', 'not_paid')
            ->where('invoice_date_due', '<', now())
            ->count();

        $shortPaid = Move::where('type', 'out_invoice')
            ->where('state', 'posted')
            ->where('payment_state', 'partial')
            ->count();

        $unposted = Move::where('type', 'out_invoice')
            ->where('state', 'draft')
            ->count();

        return [
            'overdue' => $overdue,
            'shortPaid' => $shortPaid,
            'unposted' => $unposted,
        ];
    }
}
