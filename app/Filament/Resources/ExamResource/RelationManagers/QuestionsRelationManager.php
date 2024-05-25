<?php
namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Filament\Resources\RelationManagers\RelationManager;

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
            TinyEditor::make('title')
                ->required()
                ->profile('minimal')
                ->maxLength(255)
                ->columnSpanFull(),
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
            TinyEditor::make('explanation')
                ->profile('minimal')
                ->columnSpanFull()
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
                TextColumn::make('title')
                ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state)),
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
                    ->native(false)
                    ->getSearchResultsUsing(fn (string $query) => Question::where('title', 'like', "%{$query}%")->pluck('title', 'id'))
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $this->handleQuestionSelection($state, $set, $get)),
                Forms\Components\Group::make([
                    Select::make('subject_id')
                        ->relationship('subject', 'name')
                        ->required(),
                        TextInput::make('exam_name'),
                        TextInput::make('post'),
                        DatePicker::make('date'),
                        TinyEditor::make('title')
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
                    TinyEditor::make('explanation')
                ])->hidden(fn (callable $get) => $get('showAdditionalFields'))
            ])
            ->action(function (array $data) {
                $this->handleFormSubmit($data);
            });
    }

    protected function handleQuestionSelection($state, callable $set, callable $get)
    {
        // Set visibility for additional fields
        $set('showAdditionalFields', !$state);

        // Populate the title field with the search query if no question is selected
        if (!$state) {
            $searchQuery = $get('question_id');
            $set('title', $searchQuery);
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

        $this->notify('success', 'Question attached successfully.');
    }
}
