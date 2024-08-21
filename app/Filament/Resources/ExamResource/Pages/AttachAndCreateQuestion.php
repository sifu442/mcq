<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Models\Question;
use App\Forms\Components\CKEditor;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Repeater;
use App\Filament\Resources\ExamResource;

class AttachAndCreateQuestion extends Page
{
    protected static string $resource = ExamResource::class;

    protected static string $view = 'filament.resources.exam-resource.pages.attach-and-create-question';

    public $searchResults = [];

    protected function getFormSchema(): array
    {
        return [
            CKEditor::make('title')
                ->label('Question Title')
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    $this->searchResults = Question::where('title', 'like', "%$state%")->get();
                    $set('searchResults', $this->searchResults);
                }),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'state';
    }
}
