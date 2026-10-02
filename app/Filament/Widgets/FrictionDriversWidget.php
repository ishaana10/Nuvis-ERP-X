<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;

class FrictionDriversWidget extends Widget
{
    protected string $view = 'filament.widgets.friction-drivers-widget';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'lg' => 1,
    ];

    public function getViewData(): array
    {
        $overdue = Move::where('move_type', MoveType::OUT_INVOICE->value)
            ->where('state', 'posted')
            ->where('payment_state', 'not_paid')
            ->where('invoice_date_due', '<', now())
            ->count();

        $shortPaid = Move::where('move_type', MoveType::OUT_INVOICE->value)
            ->where('state', 'posted')
            ->where('payment_state', 'partial')
            ->count();

        $unposted = Move::where('move_type', MoveType::OUT_INVOICE->value)
            ->where('state', 'draft')
            ->count();

        return [
            'overdue' => $overdue,
            'shortPaid' => $shortPaid,
            'unposted' => $unposted,
        ];
    }
}
