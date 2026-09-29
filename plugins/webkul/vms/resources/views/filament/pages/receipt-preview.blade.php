<div class="p-4 space-y-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
            FRCS Fiscal Receipt Thermal Preview
        </h3>
        <button type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded bg-primary-600 text-white font-medium hover:bg-primary-500 transition">
            Print
        </button>
    </div>

    <pre class="font-mono text-xs leading-relaxed bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-4 border border-gray-300 dark:border-gray-700 rounded shadow-sm whitespace-pre-wrap overflow-x-auto select-all max-w-sm mx-auto">
{{ $receiptText ?? 'No receipt content generated.' }}
    </pre>
</div>
