<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use stdClass;
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
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Livewire\Livewire;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([TextInput::make('name')->required()])
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
                    ->getStateUsing(function (stdClass $rowLoop, $livewire): string {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                TextColumn::make('title'),
            ])
            ->filters([
                // Add any necessary filters here
            ])
            ->headerActions([
                $this->createQuestionAttachAction(),

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

    protected function createQuestionAttachAction(): Action
    {
        return Action::make('attachQuestion')
            ->label('Attach Question')
            ->form([
                TextInput::make('question_title')
                    ->label('Search or Create Question')
                    ->placeholder('Enter question title')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Check if a question exists with the entered title
                        $question = Question::where('title', $state)->first();
                        if ($question) {
                            $set('question_id', $question->id);
                            $set('subject_id', $question->subject_id);
                            $set('exam_name', $question->exam_name);
                            $set('post', $question->post);
                            $set('date', $question->date);
                            $set('title', $question->title);
                            $set('options', $question->options);
                            $set('explanation', $question->explanation);
                        } else {
                            $set('question_id', null);
                        }
                    }),
                Livewire::mount('question-search'),
                TextInput::make('question_id')->hidden(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->visible(fn (callable $get) => !$get('question_id')),
                TextInput::make('exam_name')
                    ->visible(fn (callable $get) => !$get('question_id')),
                TextInput::make('post')
                    ->visible(fn (callable $get) => !$get('question_id')),
                DatePicker::make('date')
                    ->visible(fn (callable $get) => !$get('question_id')),
                RichEditor::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->visible(fn (callable $get) => !$get('question_id')),
                Repeater::make('options')
                    ->required()
                    ->deletable(false)
                    ->defaultItems(4)
                    ->maxItems(4)
                    ->schema([
                        TextInput::make('options'),
                        Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')
                    ])
                    ->columnSpanFull()
                    ->visible(fn (callable $get) => !$get('question_id')),
                RichEditor::make('explanation')
                    ->columnSpanFull()
                    ->visible(fn (callable $get) => !$get('question_id'))
            ])
            ->action(function (array $data) {
                if ($data['question_id']) {
                    $question = Question::find($data['question_id']);
                } else {
                    $question = Question::create($data);
                }

                // Attach the question to the exam
                $this->ownerRecord->questions()->attach($question);
            });
    }
}

