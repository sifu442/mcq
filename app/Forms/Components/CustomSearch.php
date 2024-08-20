<?php

namespace App\Forms\Components;

use Illuminate\Support\Collection;
use Filament\Forms\Components\Field;

class CustomSearch extends Field
{
    protected string $view = 'forms.components.custom-search';

    public function setUp(): void
    {
        parent::setUp();

        // Custom dehydrate logic if needed
    }

    public function search(callable $callback): self
    {
        $this->extraAttributes['searchCallback'] = $callback;

        return $this;
    }

    public function getSearchResults(): Collection
    {
        $callback = $this->extraAttributes['searchCallback'] ?? null;

        if ($callback) {
            return $callback($this->getState());
        }

        return collect();
    }
}
