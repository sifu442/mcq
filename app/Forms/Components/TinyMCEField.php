<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class TinyMCEField extends Field
{
    protected string $view = 'filament.forms.components.tiny-mce-field';

    public function getFieldView(): string
    {
        return 'filament.forms.components.tiny-mce-field';
    }
}
