<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Collection & Operational Friction Drivers
        </x-slot>

        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm font-medium mb-1">
                    <span class="text-gray-700 dark:text-gray-300">Overdue Payment Delay</span>
                    <span class="text-gray-900 dark:text-gray-100 font-bold">{{ $overdue }} Invoices</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ min(100, $overdue * 10) }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm font-medium mb-1">
                    <span class="text-gray-700 dark:text-gray-300">Short / Under-paid Invoices</span>
                    <span class="text-gray-900 dark:text-gray-100 font-bold">{{ $shortPaid }} Invoices</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ min(100, $shortPaid * 15) }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm font-medium mb-1">
                    <span class="text-gray-700 dark:text-gray-300">Pending Draft Confirmation</span>
                    <span class="text-gray-900 dark:text-gray-100 font-bold">{{ $unposted }} Invoices</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, $unposted * 10) }}%"></div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
