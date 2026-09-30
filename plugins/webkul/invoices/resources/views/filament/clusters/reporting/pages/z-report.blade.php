<x-filament-panels::page>
    <form wire:submit="$refresh">
        {{ $this->form }}
    </form>

    @php
        $data = $this->reportData;
        $summary = $data['summary'];
        $payments = $data['payments'];
        $taxBreakdown = $data['taxBreakdown'];
    @endphp

    <div class="mt-6 space-y-6">
        <!-- Z-Report Main Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <x-filament::icon icon="heroicon-o-shopping-cart" class="w-8 h-8 text-primary-500" />
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.z-report.summary.gross-sales') }}
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($summary['gross_sales_total'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $summary['total_invoices_count'] }} {{ __('invoices::filament/clusters/reporting.pages.z-report.summary.invoices') }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="w-8 h-8 text-danger-500" />
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.z-report.summary.refunds-returns') }}
                        </div>
                        <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                            ${{ number_format($summary['refunds_total'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $summary['total_refunds_count'] }} {{ __('invoices::filament/clusters/reporting.pages.z-report.summary.refunds') }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <x-filament::icon icon="heroicon-o-currency-dollar" class="w-8 h-8 text-success-500" />
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.z-report.summary.net-sales') }}
                        </div>
                        <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                            ${{ number_format($summary['net_sales_total'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ __('invoices::filament/clusters/reporting.pages.z-report.summary.untaxed') }}: ${{ number_format($summary['net_sales_untaxed'], 2) }}
                        </div>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Sales & Tax Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sales Summary Box -->
            <div class="p-6 bg-white rounded-xl shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    {{ __('invoices::filament/clusters/reporting.pages.z-report.sections.sales-summary') }}
                </h3>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('invoices::filament/clusters/reporting.pages.z-report.summary.gross-sales-untaxed') }}</dt>
                        <dd class="font-medium">${{ number_format($summary['gross_sales_untaxed'], 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('invoices::filament/clusters/reporting.pages.z-report.summary.gross-sales-tax') }}</dt>
                        <dd class="font-medium">${{ number_format($summary['gross_sales_tax'], 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm font-bold border-t pt-2">
                        <dt>{{ __('invoices::filament/clusters/reporting.pages.z-report.summary.gross-sales-total') }}</dt>
                        <dd>${{ number_format($summary['gross_sales_total'], 2) }}</dd>
                    </div>

                    <div class="flex justify-between text-sm text-danger-600 dark:text-danger-400 pt-2">
                        <dt>{{ __('invoices::filament/clusters/reporting.pages.z-report.summary.total-refunds') }}</dt>
                        <dd>-${{ number_format($summary['refunds_total'], 2) }}</dd>
                    </div>

                    <div class="flex justify-between text-base font-bold text-primary-600 dark:text-primary-400 border-t border-b py-2">
                        <dt>{{ __('invoices::filament/clusters/reporting.pages.z-report.summary.net-sales-total') }}</dt>
                        <dd>${{ number_format($summary['net_sales_total'], 2) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Tax Summary Box -->
            <div class="p-6 bg-white rounded-xl shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    {{ __('invoices::filament/clusters/reporting.pages.z-report.sections.tax-summary') }}
                </h3>
                @if(count($taxBreakdown) > 0)
                    <dl class="space-y-3">
                        @foreach($taxBreakdown as $tax)
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600 dark:text-gray-400">{{ $tax['name'] }}</dt>
                                <dd class="font-medium">${{ number_format($tax['amount'], 2) }}</dd>
                            </div>
                        @endforeach
                        <div class="flex justify-between text-base font-bold border-t pt-2">
                            <dt>{{ __('invoices::filament/clusters/reporting.pages.z-report.summary.total-tax') }}</dt>
                            <dd>${{ number_format($summary['net_sales_tax'], 2) }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-gray-500 italic">{{ __('invoices::filament/clusters/reporting.pages.z-report.messages.no-tax') }}</p>
                @endif
            </div>
        </div>

        <!-- Payments Breakdown Box -->
        <div class="p-6 bg-white rounded-xl shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                {{ __('invoices::filament/clusters/reporting.pages.z-report.sections.payments-collected') }}
            </h3>
            @if(count($payments) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2">{{ __('invoices::filament/clusters/reporting.pages.z-report.tables.journal') }}</th>
                                <th class="px-4 py-2 text-right">{{ __('invoices::filament/clusters/reporting.pages.z-report.tables.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $pay)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $pay['journal'] }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($pay['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">{{ __('invoices::filament/clusters/reporting.pages.z-report.messages.no-payments') }}</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
