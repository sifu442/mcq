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

    public function searchResults($results): static
    {
        return $this->viewData(['searchResults' => $results]);
    }
}
