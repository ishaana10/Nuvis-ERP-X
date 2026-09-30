<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="$refresh">
            {{ $this->form }}
        </form>

        @php
            $report = $this->reportData;
            $invoices = $report['invoices'];
            $groupedData = $report['groupedData'];
            $groupBy = $report['groupBy'];
            $stats = $report['stats'];
        @endphp

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-filament::section class="!p-4">
                <div class="flex items-center gap-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.invoice-report.stats.total-invoices') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($stats['total_count']) }}
                        </p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="!p-4">
                <div class="flex items-center gap-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-50 text-info-600 dark:bg-info-950 dark:text-info-400">
                        <x-filament::icon icon="heroicon-o-currency-dollar" class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.invoice-report.stats.total-amount') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($stats['total_amount'], 2) }}
                        </p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="!p-4">
                <div class="flex items-center gap-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-50 text-success-600 dark:bg-success-950 dark:text-success-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.invoice-report.stats.total-paid') }}
                        </p>
                        <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                            ${{ number_format($stats['total_paid'], 2) }}
                        </p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="!p-4">
                <div class="flex items-center gap-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-50 text-warning-600 dark:bg-warning-950 dark:text-warning-400">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('invoices::filament/clusters/reporting.pages.invoice-report.stats.total-outstanding') }}
                        </p>
                        <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                            ${{ number_format($stats['total_residual'], 2) }}
                        </p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Table Section --}}
        <x-filament::section>
            @if($invoices->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    No invoices match the selected filter criteria.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50/50 dark:bg-white/5">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.number') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.customer') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.invoice-date') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.due-date') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.type') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.total-amount') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.paid-amount') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.residual-amount') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.payment-state') }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('invoices::filament/clusters/reporting.pages.invoice-report.table.status') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @if($groupBy !== 'none' && !empty($groupedData))
                                @foreach($groupedData as $groupName => $groupInvoices)
                                    @php
                                        $groupTotal = $groupInvoices->sum('amount_total');
                                        $groupResidual = $groupInvoices->sum('amount_residual');
                                        $groupPaid = $groupTotal - $groupResidual;
                                    @endphp
                                    {{-- Group Header Row --}}
                                    <tr class="bg-gray-100/70 dark:bg-white/10 font-semibold">
                                        <td colspan="5" class="px-4 py-3 text-gray-900 dark:text-white">
                                            {{ $groupName }} ({{ $groupInvoices->count() }} invoices)
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                            ${{ number_format($groupTotal, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-success-600 dark:text-success-400">
                                            ${{ number_format($groupPaid, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-warning-600 dark:text-warning-400">
                                            ${{ number_format($groupResidual, 2) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>

                                    {{-- Group Invoices Rows --}}
                                    @foreach($groupInvoices as $invoice)
                                        @php
                                            $paidAmount = $invoice->amount_total - $invoice->amount_residual;
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $invoice->name ?: '#' . $invoice->id }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $invoice->partner?->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                                {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                                {{ $invoice->invoice_date_due ? \Carbon\Carbon::parse($invoice->invoice_date_due)->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                                {{ $invoice->move_type?->getLabel() ?? $invoice->move_type }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                ${{ number_format($invoice->amount_total, 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-right text-success-600 dark:text-success-400 whitespace-nowrap">
                                                ${{ number_format($paidAmount, 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-right text-warning-600 dark:text-warning-400 whitespace-nowrap">
                                                ${{ number_format($invoice->amount_residual, 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-center whitespace-nowrap">
                                                <x-filament::badge :color="$invoice->payment_state?->getColor() ?? 'gray'">
                                                    {{ $invoice->payment_state?->getLabel() ?? 'N/A' }}
                                                </x-filament::badge>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-center whitespace-nowrap">
                                                <x-filament::badge :color="$invoice->state?->getColor() ?? 'gray'">
                                                    {{ $invoice->state?->getLabel() ?? 'N/A' }}
                                                </x-filament::badge>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @else
                                @foreach($invoices as $invoice)
                                    @php
                                        $paidAmount = $invoice->amount_total - $invoice->amount_residual;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $invoice->name ?: '#' . $invoice->id }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $invoice->partner?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ $invoice->invoice_date_due ? \Carbon\Carbon::parse($invoice->invoice_date_due)->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ $invoice->move_type?->getLabel() ?? $invoice->move_type }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            ${{ number_format($invoice->amount_total, 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right text-success-600 dark:text-success-400 whitespace-nowrap">
                                            ${{ number_format($paidAmount, 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right text-warning-600 dark:text-warning-400 whitespace-nowrap">
                                            ${{ number_format($invoice->amount_residual, 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-center whitespace-nowrap">
                                            <x-filament::badge :color="$invoice->payment_state?->getColor() ?? 'gray'">
                                                {{ $invoice->payment_state?->getLabel() ?? 'N/A' }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-center whitespace-nowrap">
                                            <x-filament::badge :color="$invoice->state?->getColor() ?? 'gray'">
                                                {{ $invoice->state?->getLabel() ?? 'N/A' }}
                                            </x-filament::badge>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>

                        {{-- Total Footer Row --}}
                        <tfoot class="bg-gray-100/80 dark:bg-white/5 font-semibold border-t-2 border-gray-300 dark:border-white/5">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-gray-900 dark:text-white">
                                    Grand Total ({{ $stats['total_count'] }} invoices)
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white whitespace-nowrap">
                                    ${{ number_format($stats['total_amount'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-success-600 dark:text-success-400 whitespace-nowrap">
                                    ${{ number_format($stats['total_paid'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-warning-600 dark:text-warning-400 whitespace-nowrap">
                                    ${{ number_format($stats['total_residual'], 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
