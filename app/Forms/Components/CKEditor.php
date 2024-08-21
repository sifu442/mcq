<?php
namespace App\Forms\Components;

use App\Models\Question;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ck-editor';

    public ?string $state = null;
    protected bool $searchEnabled = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (?string $state): string {
            return trim($state ?? '');
        });
    }

    public function searchEnabled(bool $enabled = true): static
    {
        $this->searchEnabled = $enabled;
        return $this;
    }
}
