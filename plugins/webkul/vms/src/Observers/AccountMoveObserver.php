<?php

namespace Webkul\Vms\Observers;

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;
use Webkul\Vms\Models\VmsSetting;
use Webkul\Vms\Services\VmsService;

class AccountMoveObserver
{
    public function updated(Move $move): void
    {
        try {
            $setting = VmsSetting::where('company_id', $move->company_id ?? current_company_id())->first();

            if (! $setting || ! $setting->is_active) {
                return;
            }

            if ($move->isDirty('state') && $move->state === MoveState::POSTED && $move->isSaleDocument(true)) {
                $vmsService = app(VmsService::class);
                $transactionType = $move->move_type->value === 'out_refund' ? 'Refund' : 'Sale';
                $vmsService->fiscalizeAccountMove($move, 'Normal', $transactionType);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
