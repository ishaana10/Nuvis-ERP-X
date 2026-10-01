<?php

namespace Webkul\Payroll\Filament\Admin\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Enums\PayrollRunState;
use Webkul\Payroll\Filament\Admin\Resources\PayrollRunResource\Pages\ListPayrollRuns;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Services\AccountingPoster;
use Webkul\Payroll\Services\PayslipGenerator;

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-play-circle';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return \Webkul\Support\Enums\NavigationGroup::Payroll;
    }

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('period_id')
                    ->relationship('period', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('state')
                    ->options(PayrollRunState::class)
                    ->default(PayrollRunState::DRAFT)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('period.name')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->sortable(),
                TextColumn::make('payslips_count')
                    ->counts('payslips')
                    ->label('Payslips'),
                TextColumn::make('state')
                    ->badge()
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('generate_payslips')
                    ->label('Generate Payslips')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('info')
                    ->action(function (PayrollRun $record, PayslipGenerator $generator) {
                        $generator->generateForRun($record);
                        $record->update(['processed_at' => now()]);
                        Notification::make()
                            ->title('Payslips generated successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('post_accounting')
                    ->label('Post Entries')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (PayrollRun $record, AccountingPoster $poster) {
                        $poster->postRun($record);
                        $record->update(['state' => PayrollRunState::DONE]);
                        Notification::make()
                            ->title('Payroll entries posted to accounting.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollRuns::route('/'),
        ];
    }
}
