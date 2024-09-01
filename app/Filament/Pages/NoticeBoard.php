<?php

namespace App\Filament\Pages;

use App\Models\Notice;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Forms\Components\CKEditor;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class NoticeBoard extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.notice-board';

    public function mount(): void
    {
        $notice = Notice::first();
        $this->data = $notice ? $notice->toArray() : [];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CKEditor::make('text')
                    ->label('Notice Text')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $notice = Notice::firstOrCreate(['id' => 1], $data);
        $notice->update($data);

        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Notice updated successfully')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Submit')
                ->submit('save'),
        ];
    }
}

