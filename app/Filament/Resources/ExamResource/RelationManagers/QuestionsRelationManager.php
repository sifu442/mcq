<?php
namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;

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
