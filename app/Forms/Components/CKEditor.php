<?php
namespace App\Forms\Components;

use App\Models\Question;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ck-editor';

    public ?string $state = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (?string $state): string {
            return trim($state ?? '');
        });

        // Listen for the fillEditor event to update the CKEditor content
        $this->listeners(['fillEditor' => 'fillEditor']);
    }

    public function fillEditor($content)
    {
        $this->state = $content;
    }
}
