<?php
namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Forms\Components\CKEditor;
use App\Forms\Components\CustomSearch;
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

    public function search($query)
{
    return Question::where('content', 'like', '%'.$query.'%')->get(['id', 'title', 'content']);
}

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
                Action::make('create-and-attach')
                    ->modalContent()
                ,
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
}
