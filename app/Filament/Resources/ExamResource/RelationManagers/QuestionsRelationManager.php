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
use Filament\Forms\Components\Placeholder;
use Filament\Resources\RelationManagers\RelationManager;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public $search_results = [];


    public function form(Form $form): Form
    {
        $latestExam = Question::latest()->first();

        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([TextInput::make('name')->required()])
                ->preload()
                ->required()
                ->default($latestExam->subject_id ?? null),
            TextInput::make('previous_exam')->label('Exam Name')->default($latestExam->previous_exam ?? ''),
            TextInput::make('post')->default($latestExam->post ?? ''),
            DatePicker::make('date')->native(false)->default($latestExam->date ?? ''),
            CKEditor::make('title')->required()->columnSpanFull()->default($latestExam->title ?? ''),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->schema([
                    CKEditor::make('options'),
                    Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')
                ])
                ->columnSpanFull()
                ->default($latestExam->options ?? []),
            CKEditor::make('explanation')->columnSpanFull()->default($latestExam->explanation ?? ''),
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
                TextColumn::make('title')->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state)),
            ])
            ->filters([])
            ->headerActions([
                Action::make('Attach')
                    ->form([
                        Select::make('subject_id')
                            ->native(false)
                            ->relationship('subject', 'name')
                            ->createOptionForm([TextInput::make('name')->required()])
                            ->required(),
                        TextInput::make('previous_exam')->label('Exam Name'),
                        TextInput::make('post'),
                        DatePicker::make('date')->native(false),
                        TextInput::make('title')
                            ->columnSpanFull()
                            ->required()
                            ->live(onBlur: false, debounce: 500)
                            ->afterStateUpdated(function (?string $state, $set) {
                                if (strlen($state) >= 3) {
                                    $searchResults = static::searchQuestions($state);
                                    $set('search_results', $searchResults);
                                } else {
                                    $set('search_results', []);
                                }
                            }),
                        Placeholder::make('search_results')
                            ->label('')
                            ->content(function ($get, $set) {
                                $questions = $get('search_results') ?? [];
                                $html = '<ul>';

                                if (empty($questions)) {
                                    $html .= '<li>' . __('No matching questions found.') . '</li>';
                                } else {
                                    foreach ($questions as $question) {
                                        $html .= '<li>
                                            <button
                                                type="button"
                                                wire:click="fillQuestionData(' . $question['id'] . ')"
                                                class="text-left w-full"
                                            >
                                                ' . htmlspecialchars($question['title']) . '
                                            </button>
                                        </li>';
                                    }
                                }

                                $html .= '</ul>';
                                return new HtmlString($html);
                            })
                            ->columnSpanFull(),
                        Repeater::make('options')
                            ->required()
                            ->deletable(false)
                            ->defaultItems(4)
                            ->maxItems(4)
                            ->schema([
                                CKEditor::make('options'),
                                Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')
                            ])
                            ->columnSpanFull(),
                        CKEditor::make('explanation')->columnSpanFull(),
                    ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function searchQuestions(?string $searchTerm): array
    {
        if (blank($searchTerm)) {
            return [];
        }

        return Question::where('title', 'like', '%' . $searchTerm . '%')
            ->limit(10)
            ->get(['id', 'title'])
            ->toArray();
    }

    public function fillQuestionData($questionId)
    {
        $question = Question::find($questionId);

        if ($question) {
            $this->emit('fillFormData', [
                'subject_id' => $question->subject_id,
                'previous_exam' => $question->previous_exam,
                'post' => $question->post,
                'date' => $question->date,
                'title' => $question->title,
                'options' => $question->options,
                'explanation' => $question->explanation,
            ]);
        }
    }

    protected function getListeners(): array
    {
        return [
            'fillFormData' => 'updateFormData',
        ];
    }

    public function updateFormData($data)
    {
        $this->form->fill($data);
    }
}
