<x-filament-panels::page>
    <form wire:submit="process" class="fi-form grid gap-y-6">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">
                Execute Payroll & Post Entries
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
