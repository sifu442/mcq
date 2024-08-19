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

    protected $listeners = ['fillQuestionData'];

    public $search_results = [];

    public $subject_id;
    public $previous_exam;
    public $post;
    public $date;
    public $title;
    public $options = [];
    public $explanation;

    public function form(Form $form): Form
    {
        $latestExam = Question::latest()->first();

        if (is_null($latestExam)) {
            return $form->schema([
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->createOptionForm([TextInput::make('name')->required()])
                    ->preload()
                    ->required(),
                TextInput::make('previous_exam')->label('Exam Name'),
                TextInput::make('post'),
                DatePicker::make('date')->native(false),
                CKEditor::make('title')->required()->columnSpanFull(),
                Repeater::make('options')
                    ->required()
                    ->deletable(false)
                    ->defaultItems(4)
                    ->maxItems(4)
                    ->schema([CKEditor::make('options'), Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')])
                    ->columnSpanFull(),
                CKEditor::make('explanation')->columnSpanFull(),
            ]);
        }

        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([TextInput::make('name')->required()])
                ->default($latestExam->subject_id)
                ->preload()
                ->required(),
            TextInput::make('previous_exam')
                ->label('Exam Name')
                ->default($latestExam->previous_exam),
            TextInput::make('post')->default($latestExam->post),
            DatePicker::make('date')
                ->default($latestExam->date)
                ->native(false),
            CKEditor::make('title')->columnSpanFull()->required(),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->schema([CKEditor::make('options'), Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')])
                ->columnSpanFull(),
            CKEditor::make('explanation')->columnSpanFull(),
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
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Attach')->form([
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
                        ->columnSpanFull()
                        ->columnSpanFull(),
                    Repeater::make('options')
                        ->required()
                        ->deletable(false)
                        ->defaultItems(4)
                        ->maxItems(4)
                        ->schema([CKEditor::make('options'), Checkbox::make('is_correct')->fixIndistinctState()->name('Correct Answer')])
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
        // Manually set each field
        $this->form->getState()['subject_id'] = $question->subject_id;
        $this->form->getState()['previous_exam'] = $question->previous_exam;
        $this->form->getState()['post'] = $question->post;
        $this->form->getState()['date'] = $question->date;
        $this->form->getState()['title'] = $question->title;
        $this->form->getState()['options'] = $question->options;
        $this->form->getState()['explanation'] = $question->explanation;

        // Clear search results after selection
        $this->search_results = null;
    }
}


}
