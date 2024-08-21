<?php
namespace App\Forms\Components;

use App\Models\Question;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ck-editor';

    public ?string $state = null;
    public array $searchResults = [];
    protected bool $searchEnabled = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (?string $state): string {
            return trim($state ?? '');
        });

        if ($this->searchEnabled) {
            $this->afterStateUpdated(function (?string $state) {
                if ($this->searchEnabled && strlen($state) > 2) {
                    $this->search($state);
                }
            });
        }
    }

    public function searchEnabled(bool $enabled = true): static
    {
        $this->searchEnabled = $enabled;
        return $this;
    }

    public function search(?string $query): void
    {
        if ($query) {
            // Example search logic
            $this->searchResults = Question::where('title', 'like', '%' . $query . '%')->get()->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectResult(string $result): void
    {
        $this->state = $result;
        $this->searchResults = [];
    }
}
