<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ck-editor';

    public function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (string $state): string {
            return trim($state);
        });
    }
}
