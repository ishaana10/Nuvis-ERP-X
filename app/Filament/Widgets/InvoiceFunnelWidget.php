<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;

class InvoiceFunnelWidget extends Widget
{
    protected string $view = 'filament.widgets.invoice-funnel-widget';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $draft = Move::where('move_type', MoveType::OUT_INVOICE->value)->where('state', 'draft')->count();
        $posted = Move::where('move_type', MoveType::OUT_INVOICE->value)->where('state', 'posted')->count();
        $paid = Move::where('move_type', MoveType::OUT_INVOICE->value)->where('state', 'posted')->where('payment_state', 'paid')->count();
        $partial = Move::where('move_type', MoveType::OUT_INVOICE->value)->where('state', 'posted')->where('payment_state', 'partial')->count();

        return [
            'draft' => $draft,
            'posted' => $posted,
            'paid' => $paid,
            'partial' => $partial,
        ];
    }
}
