<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ExamsRelationManager extends RelationManager
{
    protected static string $relationship = 'exams';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->translateLabel(),
            Select::make('course_id')->relationship('course', 'title')->required(),
            TextInput::make('syllabus')->required()->translateLabel(),
            TextInput::make('duration')->required()->numeric()->suffix('Minutes')->translateLabel(),
            TextInput::make('delay_days')->required()->numeric()->suffix('Days')->translateLabel(),
            TextInput::make('available_for_hours')->required()->numeric()->suffix('Hours')->translateLabel(),
            Select::make('score')
                ->required()
                ->options([
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ]),
            Select::make('penalty')
                ->required()
                ->options([
                    '0.25' => '0.25',
                    '0.50' => '0.50',
                    '0.70' => '0.50',
                    '1' => '1',
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('index')
                    ->getStateUsing(function (stdClass $rowLoop, $livewire): string {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                TextColumn::make('name'),
                TextColumn::make('questions_count')->label('Number of Questions')->counts('questions')->translateLabel('Question'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
