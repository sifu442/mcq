<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
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
                    TextInput::make('option'),
                    Checkbox::make('is_correct')->fixIndistinctState()->label('Correct Answer')
                ])
                ->columnSpanFull(),
            RichEditor::make('explanation')->columnSpanFull()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->getStateUsing(function ($rowLoop, $livewire): string {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                TextColumn::make('title'),
            ])
            ->filters([
                // Add your filters here
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
                Action::make('attachQuestion')
                    ->label('Attach Question')
                    ->form([
                        Select::make('question_id')
                            ->label('Select Question')
                            ->relationship('questions', 'title')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('title')->required(),
                                Select::make('subject_id')->relationship('subject', 'name')->required(),
                                RichEditor::make('title')->required()->maxLength(255)->columnSpanFull(),
                                Repeater::make('options')
                                    ->required()
                                    ->deletable(false)
                                    ->defaultItems(4)
                                    ->maxItems(4)
                                    ->schema([
                                        TextInput::make('option'),
                                        Checkbox::make('is_correct')->fixIndistinctState()->label('Correct Answer')
                                    ])
                                    ->columnSpanFull(),
                                RichEditor::make('explanation')->columnSpanFull()
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $question = Question::find($data['question_id']);

                        if (!$question) {
                            $question = Question::create([
                                'title' => $data['title'],
                                'subject_id' => $data['subject_id'],
                                'options' => $data['options'],
                                'explanation' => $data['explanation'],
                            ]);
                        }

                        $this->ownerRecord->questions()->attach($question);
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ]);
    }
}
