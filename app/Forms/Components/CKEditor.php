<?php
namespace App\Forms\Components;

use App\Models\Question;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ck-editor';

    public function setUp(): void
    {
        parent::setUp();

    }

}
