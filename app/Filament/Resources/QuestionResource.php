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
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                    ->getStateUsing(function (stdClass $rowLoop, $livewire): string {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(function ($record) {
                        return [
                            Select::make('question_id')
                                ->label('Question')
                                ->relationship('questions', 'title')
                                ->searchable()
                                ->getSearchResultsUsing(function ($search) {
                                    return Question::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id');
                                })
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    if (!$state) {
                                        $set('new_question', true);
                                    }
                                })
                                ->required(),
                            TextInput::make('new_question_title')
                                ->label('New Question Title')
                                ->visible(fn ($get) => $get('new_question'))
                                ->required(fn ($get) => $get('new_question')),
                            // Add more fields here for the new question creation
                        ];
                    })
                    ->action(function ($data) {
                        if ($data['new_question']) {
                            $question = Question::create([
                                'title' => $data['new_question_title'],
                                // Populate other fields for new question
                            ]);
                            $this->ownerRecord->questions()->attach($question);
                        } else {
                            $this->ownerRecord->questions()->attach($data['question_id']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ])
            ]);
    }
}
