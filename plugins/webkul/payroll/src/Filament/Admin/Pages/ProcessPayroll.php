<?php

namespace Webkul\Payroll\Filament\Admin\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Webkul\Payroll\Models\PayrollPeriod;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Services\AccountingPoster;
use Webkul\Payroll\Services\PayslipGenerator;

class ProcessPayroll extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $title = 'Process Payroll';

    protected static ?int $navigationSort = 8;

    protected string $view = 'payroll::pages.process-payroll';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('run_name')
                    ->label('Payroll Run Name')
                    ->required()
                    ->default('Payroll Run - '.now()->format('F Y')),
                Select::make('period_id')
                    ->label('Payroll Period')
                    ->options(PayrollPeriod::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
            ])
            ->statePath('data');
    }

    public function process(PayslipGenerator $generator, AccountingPoster $poster): void
    {
        $formData = $this->form->getState();

        $run = PayrollRun::create([
            'name'      => $formData['run_name'],
            'period_id' => $formData['period_id'] ?? null,
            'company_id'=> current_company_id(),
        ]);

        $payslips = $generator->generateForRun($run);
        $poster->postRun($run);

        Notification::make()
            ->title('Payroll Processed Successfully')
            ->body('Generated and posted '.$payslips->count().' payslips for '.$run->name)
            ->success()
            ->send();
    }
}
