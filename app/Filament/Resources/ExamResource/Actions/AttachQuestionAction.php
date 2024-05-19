<?php

namespace App\Filament\Resources\ExamResource\Actions;

use Filament\Forms;
use Filament\Tables;
use App\Models\Question;
use Filament\Resources\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AttachQuestionAction extends Action
{
    protected static string $name = 'attachQuestion';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('search')
                    ->label('Search Question')
                    ->placeholder('Enter question title to search')
                    ->reactive()
                    ->afterStateUpdated(fn (string $state, $set, $get) => $this->searchQuestion($state, $set, $get)),
            ]);
    }

    public function handle(array $data, Model $record, Collection $records): void
    {
        $questionId = $data['search'] ?? null;

        if ($questionId) {
            $question = Question::find($questionId);

            if ($question) {
                $record->questions()->attach($question);
                $this->success('Question attached successfully.');
                return;
            }
        }

        // If no question is found, render additional fields for creating a new question
        $this->renderCreateQuestionForm($data, $record);
    }

    protected function searchQuestion(string $title, $set, $get): void
    {
        $questions = Question::where('title', 'like', '%' . $title . '%')->get();
        $set('results', $questions->pluck('title', 'id')->toArray());
    }

    protected function renderCreateQuestionForm(array $data, Model $record): void
    {
        $this->form([
            Forms\Components\Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([Forms\Components\TextInput::make('name')->required()])
                ->required(),
            Forms\Components\TextInput::make('title')
                ->label('Question Title')
                ->required(),
            Forms\Components\Repeater::make('options')
                ->label('Options')
                ->schema([
                    Forms\Components\TextInput::make('option')
                        ->label('Option Text')
                        ->required(),
                    Forms\Components\Checkbox::make('is_correct')
                        ->label('Correct Answer')
                        ->required(),
                ])
                ->minItems(4)
                ->maxItems(4),
            Forms\Components\RichEditor::make('explanation')
                ->label('Explanation')
                ->columnSpanFull(),
        ]);

        // Store the new question and attach it to the exam
        $this->success('New question created and attached successfully.');
    }
}
