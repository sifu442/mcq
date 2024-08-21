<?php
namespace App\Forms\Components;

use App\Models\Question;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ck-editor';

    public ?string $state = null;
    public ?string $searchQuery = null;
    public array $searchResults = [];
    protected bool $searchEnabled = false; // Track if search is enabled

    public function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (?string $state): string {
            return trim($state ?? '');
        });

        // Set up search if enabled
        if ($this->searchEnabled) {
            $this->afterStateUpdated(function (?string $state) {
                if ($this->searchEnabled && strlen($state) > 2) {
                    $this->search();
                }
            });
        }
    }

    public function search(): void
    {
        if ($this->searchQuery) {
            // Replace with your logic to search related records
            $this->searchResults = Question::where('title', 'like', '%' . $this->searchQuery . '%')->get()->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectResult(string $result): void
    {
        $this->state = $result;
        $this->searchResults = [];
    }

    // Method to enable search
    public function searchEnabled(bool $enabled = true): static
    {
        $this->searchEnabled = $enabled;

        return $this;
    }
}
