<?php
namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Forms\Components\CKEditor;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Webbingbrasil\FilamentCopyActions\Forms\Actions\CopyAction;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        $latestExam = Question::latest()->first();

        if(is_null($latestExam)) {
            return $form->schema([
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->createOptionForm([
                        TextInput::make('name')->required()
                    ])
                    ->preload()
                    ->required(),
                TextInput::make('previous_exam')
                    ->label('Exam Name'),
                TextInput::make('post'),
                DatePicker::make('date')
                    ->native(false),
                CKEditor::make('title')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('options')
                    ->required()
                    ->deletable(false)
                    ->defaultItems(4)
                    ->maxItems(4)
                    ->schema([
                        CKEditor::make('options'),
                        Checkbox::make('is_correct')
                            ->fixIndistinctState()
                            ->name('Correct Answer'),
                    ])
                    ->columnSpanFull(),
                CKEditor::make('explanation')
                    ->columnSpanFull()
            ]);
        }

        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required()
                ])
                ->default($latestExam->subject_id)
                ->preload()
                ->required(),
            TextInput::make('previous_exam')
                ->label('Exam Name')
                ->default($latestExam->previous_exam),
            TextInput::make('post')
                ->default($latestExam->post),
            DatePicker::make('date')
                ->default($latestExam->date)
                ->native(false),
            CKEditor::make('title')
                ->columnSpanFull()

                ->required(),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->schema([
                    CKEditor::make('options')
                    ,
                    Checkbox::make('is_correct')
                        ->fixIndistinctState()
                        ->name('Correct Answer'),
                ])
                ->columnSpanFull(),
            CKEditor::make('explanation')
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
                Action::make('Attach')
                    ->action(function (array $data): void {
                        if ($data['search-question']) {
                            // Attach the selected question to the Exam model
                            $this->ownerRecord->questions()->attach($data['search-question']);
                        } else {
                            // Create a new question and attach it to the Exam model
                            $newQuestion = Question::create([
                                'subject_id' => $data['subject_id'],
                                'previous_exam' => $data['previous_exam'],
                                'post' => $data['post'],
                                'date' => $data['date'],
                                'title' => $data['title'],
                                'options' => $data['options'],
                                'explanation' => $data['explanation'],
                            ]);
                            $this->ownerRecord->questions()->attach($newQuestion->id);
                        }
                    })
                    ->form([
                        Select::make('search-question')
                            ->label('Search Questions')
                            ->native(false)
                            ->searchable()
                            ->prefixAction(CopyAction::make()->copyable(fn ($component) => $component->getOptionLabel()))
                            ->getSearchResultsUsing(fn (string $search): array =>
                                Question::where('title', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('title', 'id')
                                    ->toArray()
                            )
                            ->afterStateUpdated(function (Set $set, $state) {
                                $question = Question::find($state);
                                if ($question) {
                                    $set('title', $question->title);
                                }
                            })
                            ->getOptionLabelUsing(fn ($value): ?string => Question::find($value)?->title)
                            ->live(onBlur: true),
                        Select::make('subject_id')
                        ->native(false)
                        ->relationship('subject', 'name')
                        ->createOptionForm([
                            TextInput::make('name')->required()
                        ])
                        ->required()
                        ->visible(fn ($get) => !$get('search-question')),
                        TextInput::make('previous_exam')
                            ->label('Exam Name')
                            ->visible(fn ($get) => !$get('search-question')),
                        TextInput::make('post')
                            ->visible(fn ($get) => !$get('search-question')),
                        DatePicker::make('date')
                            ->native(false)
                            ->visible(fn ($get) => !$get('search-question')),
                        CKEditor::make('title')
                            ->columnSpanFull()
                            ->required()
                            ->visible(fn ($get) => !$get('search-question')),
                        Repeater::make('options')
                            ->required()
                            ->deletable(false)
                            ->defaultItems(4)
                            ->maxItems(4)
                            ->schema([
                                CKEditor::make('options'),
                                Checkbox::make('is_correct')
                                    ->fixIndistinctState()
                                    ->name('Correct Answer'),
                            ])
                            ->columnSpanFull()
                            ->visible(fn ($get) => !$get('search-question')),
                        CKEditor::make('explanation')
                            ->columnSpanFull()
                            ->visible(fn ($get) => !$get('search-question')),

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
