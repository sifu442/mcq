<?php

namespace App\Filament\Resources\ExamResponsesResource\Pages;

use App\Filament\Resources\ExamResponsesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamResponses extends ListRecords
{
    protected static string $resource = ExamResponsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
