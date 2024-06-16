<?php

namespace App\Filament\Resources\ExamResponsesResource\Pages;

use App\Filament\Resources\ExamResponsesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamResponses extends EditRecord
{
    protected static string $resource = ExamResponsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
