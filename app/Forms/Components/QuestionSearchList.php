<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class QuestionSearchList extends Field
{
    protected string $view = 'filament.forms.components.question-search-list';

    protected array $questions = [];

    public static function make(string $name): static
    {
        $static = parent::make($name);
        $static->configure();

        return $static;
    }

    public function questions(array $questions): static
    {
        $this->questions = $questions;

        return $this;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }
}
