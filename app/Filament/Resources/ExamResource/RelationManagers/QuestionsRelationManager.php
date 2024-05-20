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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
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
                    TextInput::make('options'),
                    Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')
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
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
                $this->getQuestionAttachAction(),
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

    protected function getQuestionAttachAction(): Action
    {
        return Action::make('questionAttach')
            ->label('Attach Question')
            ->form([
                Select::make('question_id')
                    ->label('Search Question')
                    ->relationship('questions', 'title')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => Question::where('title', 'like', "%{$query}%")->pluck('title', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $this->handleQuestionSelection($state, $set, $get)),
                Forms\Components\Group::make([
                    Select::make('subject_id')
                        ->relationship('subject', 'name')
                        ->required(),
                    RichEditor::make('title')
                        ->required()
                        ->maxLength(255),
                    Repeater::make('options')
                        ->required()
                        ->deletable(false)
                        ->defaultItems(4)
                        ->maxItems(4)
                        ->schema([
                            TextInput::make('options'),
                            Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')
                        ]),
                    RichEditor::make('explanation')
                ])->hidden(fn (callable $get) => !$get('showAdditionalFields'))
            ])
            ->action(function (array $data) {
                $this->handleFormSubmit($data);
            });
    }

    protected function handleQuestionSelection($state, callable $set, callable $get)
    {
        // Set visibility for additional fields
        $set('showAdditionalFields', !$state);
        // If no existing question is selected, set the search text to the title field
        if (!$state) {
            $searchText = $get('question_id.search');
            if ($searchText) {
                $set('title', $searchText);
            }
        }
    }

    protected function handleFormSubmit(array $data)
    {
        if (isset($data['question_id'])) {
            // Attach existing question to exam
            $this->ownerRecord->questions()->attach($data['question_id']);
        } else {
            // Create new question and attach to exam
            $question = Question::create([
                'title' => $data['title'],
                'subject_id' => $data['subject_id'],
                'options' => $data['options'],
                'explanation' => $data['explanation'],
            ]);

            $this->ownerRecord->questions()->attach($question->id);
        }

    }
}
