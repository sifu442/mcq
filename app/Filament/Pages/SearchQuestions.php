<?php

namespace App\Filament\Pages;

use App\Forms\Components\CKEditor;
use App\Models\Question;
use Filament\Forms\Components\View;
use Filament\Pages\Page;

class SearchQuestions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.search-questions';
    
    public $searchResults = [];

    protected function getFormSchema(): array
    {
        return [
            CKEditor::make('title')
                ->label('Search Title')
                ->ckeditor()
                ->afterStateUpdated(fn ($state) => $this->performSearch($state))
                ->required(),
            View::make('filament.components.search-results')
                ->label('Results')
                ->visible(fn () => count($this->searchResults) > 0),
        ];
    }

    public function performSearch($title)
    {
        $this->searchResults = Question::where('title', 'like', "%{$title}%")->pluck('title')->toArray();
    }
}
