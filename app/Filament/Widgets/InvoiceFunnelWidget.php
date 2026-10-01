<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Models\Move;

class InvoiceFunnelWidget extends Widget
{
    protected string $view = 'filament.widgets.invoice-funnel-widget';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $draft = Move::where('type', 'out_invoice')->where('state', 'draft')->count();
        $posted = Move::where('type', 'out_invoice')->where('state', 'posted')->count();
        $paid = Move::where('type', 'out_invoice')->where('state', 'posted')->where('payment_state', 'paid')->count();
        $partial = Move::where('type', 'out_invoice')->where('state', 'posted')->where('payment_state', 'partial')->count();

        return [
            'draft' => $draft,
            'posted' => $posted,
            'paid' => $paid,
            'partial' => $partial,
        ];
    }
}
