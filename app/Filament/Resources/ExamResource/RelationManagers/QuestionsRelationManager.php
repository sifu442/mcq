<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use App\Models\Question;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Livewire\Livewire;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required()
                ])
                ->required(),
            TextInput::make('exam_name'),
            TextInput::make('post'),
            DatePicker::make('date'),
            RichEditor::make('title')->required()->maxLength(255)->columnSpanFull(),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->schema([
                    TextInput::make('options'),
                    Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')
                ])
                ->columnSpanFull(),
            RichEditor::make('explanation')->columnSpanFull()
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('Index')
                    ->getStateUsing(function ($rowLoop, $livewire) {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([])
            ->headerActions([
                Action::make('Question Attach & Creation')
                ->columns([
                    TextColumn::make('questions')->searchable()
                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
