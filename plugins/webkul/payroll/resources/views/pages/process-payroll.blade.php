<x-filament-panels::page>
    <x-filament-panels::form wire:submit="process">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Execute Payroll & Post Entries
        </x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page>
