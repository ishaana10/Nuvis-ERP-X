<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Invoicing Progression Funnel
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">1. Draft Invoices</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $draft }}</div>
            </div>
            <div class="p-4 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="text-sm font-medium text-blue-600 dark:text-blue-400">2. Posted Invoices</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $posted }}</div>
            </div>
            <div class="p-4 bg-amber-50 dark:bg-amber-950/30 rounded-lg border border-amber-200 dark:border-amber-800">
                <div class="text-sm font-medium text-amber-600 dark:text-amber-400">3. Short/Partial Paid</div>
                <div class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ $partial }}</div>
            </div>
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 rounded-lg border border-emerald-200 dark:border-emerald-800">
                <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400">4. Fully Paid</div>
                <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">{{ $paid }}</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
