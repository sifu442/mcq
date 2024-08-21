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

}
