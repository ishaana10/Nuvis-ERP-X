<?php

namespace Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Nuvis\FijiPayroll\Models\SalarySlip;

class SalarySlipsRelationManager extends RelationManager
{
    protected static string $relationship = 'salarySlips';

    protected static ?string $title = 'Salary Slips';

    protected static ?string $recordTitleAttribute = 'employee_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_name')->disabled(),
                TextInput::make('gross')->numeric()->prefix('FJD')->disabled(),
                TextInput::make('employee_fnpf')->numeric()->prefix('FJD')->disabled(),
                TextInput::make('paye')->numeric()->prefix('FJD')->disabled(),
                TextInput::make('net_pay')->numeric()->prefix('FJD')->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('employee_name')
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('employee_number')->toggleable(),
                Tables\Columns\TextColumn::make('gross')->money('FJD'),
                Tables\Columns\TextColumn::make('employee_fnpf')->label('Emp FNPF')->money('FJD'),
                Tables\Columns\TextColumn::make('employer_fnpf')->label('Er FNPF')->money('FJD')->toggleable(),
                Tables\Columns\TextColumn::make('paye')->money('FJD'),
                Tables\Columns\TextColumn::make('net_pay')->money('FJD')->weight('bold'),
                Tables\Columns\TextColumn::make('employer_cost')->money('FJD')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                ViewAction::make(),
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (SalarySlip $record) => route('fiji-payroll.payslip.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
