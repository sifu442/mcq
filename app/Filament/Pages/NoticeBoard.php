<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Forms\Components\CKEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class NoticeBoard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.notice-board';

    public ?array $data = [];

    use InteractsWithForms;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CKEditor::make('text')
                    ->required(),
            ])
            ->statePath('data');
    }

}
