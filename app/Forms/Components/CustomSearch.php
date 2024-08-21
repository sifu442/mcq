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
    }

    public function options(callable $callback)
    {
        $this->configureOptionCallback($callback);
        return $this;
    }

    protected function configureOptionCallback(callable $callback): void
    {
        $this->data['options'] = $callback();
    }

    public function getOptions()
    {
        return $this->data['options'] ?? [];
    }
}
