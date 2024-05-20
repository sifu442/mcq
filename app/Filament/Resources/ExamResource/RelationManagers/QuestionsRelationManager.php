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
use Illuminate\Support\Collection;

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
        return Action::make('Question Attach')
            ->form(function () {
                return [
                    Select::make('question_id')
                        ->label('Select Question')
                        ->options(Question::all()->pluck('title', 'id')->toArray())
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) {
                                $set('showCreateForm', true);
                            } else {
                                $set('showCreateForm', false);
                            }
                        })
                        ->required(),
                    Forms\Components\Hidden::make('showCreateForm')
                        ->default(false)
                        ->reactive(),
                    Forms\Components\Fieldset::make('Create New Question')
                        ->label('')
                        ->schema([
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
                                ->schema([TextInput::make('options'), Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')])
                                ->columnSpanFull(),
                            RichEditor::make('explanation')->columnSpanFull(),
                        ])
                        ->visible(fn ($get) => $get('showCreateForm')),
                ];
            })
            ->action(function ($data) {
                $questionId = $data['question_id'] ?? null;

                if (!$questionId) {
                    $question = Question::create([
                        'subject_id' => $data['subject_id'],
                        'title' => $data['title'],
                        'exam_name' => $data['exam_name'],
                        'post' => $data['post'],
                        'date' => $data['date'],
                        'explanation' => $data['explanation'],
                    ]);

                    foreach ($data['options'] as $option) {
                        $question->options()->create([
                            'option' => $option['option'],
                            'is_correct' => $option['is_correct'],
                        ]);
                    }

                    $questionId = $question->id;
                }

                $this->ownerRecord->questions()->attach($questionId);
            })
            ->label('Attach Question');
    }
}
