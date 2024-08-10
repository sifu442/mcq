<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use stdClass;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Forms\Components\CKEditor;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;

class ExamsRelationManager extends RelationManager
{
    protected static string $relationship = 'exams';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->translateLabel(),
            Select::make('course_id')
                ->relationship('courses', 'title')
                ->multiple()
                ->preload(5)
                ->label('Course'),
            TextInput::make('duration')
                ->required()
                ->numeric()
                ->default(30)
                ->suffix('Minutes')
                ->translateLabel(),
            TextInput::make('gap')
                ->required()
                ->numeric()
                ->suffix('Days')
                ->translateLabel()
                ->default(3),
            TextInput::make('participation_time')
                ->required()
                ->numeric()
                ->suffix('Hours')
                ->default(24)
                ->translateLabel(),
            Select::make('score')
                ->native(false)
                ->default('1')
                ->required()
                ->options([
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '2' => '2' ,
                    '3' => '3' ,
                    '4' => '4' ,
                    '5' => '5' ,
                    '6' => '6' ,
                    '7' => '7' ,
                    '8' => '8' ,
                    '9' => '9' ,
                    '10' =>'10',
                ]),
            Select::make('penalty')
                ->required()
                ->default('0.50')
                ->options([
                    '0.25' => '0.25',
                    '0.50' => '0.50',
                    '0.70' => '0.50',
                    '1.00' => '1.00' ,
                    '1.25' => '1.25',
                    '1.50' => '1.50',
                    '2.00' => '2.00',

                ]),
            CKEditor::make('syllabus')
                ->required()
                ->translateLabel()
                ->columnSpanFull(),
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
                TextColumn::make('questions_count')
                    ->label('Number of Questions')
                    ->counts('questions')
                    ->translateLabel('Question'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                ])]);
    }
}
