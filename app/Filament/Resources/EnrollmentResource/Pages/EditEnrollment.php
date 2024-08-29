<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EnrollmentResource;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Adjust Routine Dates')
                ->form([
                    TextInput::make('days')
                        ->label('Increase Days')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $days = $data['days'];

                    $this->record->routine = collect($this->record->routine)->map(function ($item) use ($days) {
                        $item['start_time'] = Carbon::parse($item['start_time'])->addDays($days)->format('Y-m-d H:i:s');
                        $item['end_time'] = Carbon::parse($item['end_time'])->addDays($days)->format('Y-m-d H:i:s');
                        return $item;
                    })->toArray();

                    $this->record->save();

                })
                ->after(function () {
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->getKey()]));
                }),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Prevent updating the 'exam_id' inside the repeater when filling the form.
        if (isset($data['routine'])) {
            foreach ($data['routine'] as &$routine) {
                // Assuming you want to retain the existing exam_id, do not modify it here
                $routine['exam_id'] = $routine['exam_id'] ?? null;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure that the routine field is saved correctly.
        if (isset($data['routine'])) {
            foreach ($data['routine'] as &$routine) {
                // This is where you could make additional adjustments if needed before saving.
                // For example, ensuring the 'exam_id' is properly saved.
            }
        }

        return $data;
    }
}
