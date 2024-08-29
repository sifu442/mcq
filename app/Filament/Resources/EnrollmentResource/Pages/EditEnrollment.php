<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
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
        // Prevent `starts_from` field from affecting `routine` data
        // Ensure the exams selected in `routine` are retained
        if (isset($data['routine']) && is_array($data['routine'])) {
            foreach ($data['routine'] as &$routineItem) {
                // If there's a specific logic to prevent updates, apply it here.
                // For example, reloading from the database if necessary:
                $routineItem['exam_id'] = $routineItem['exam_id'] ?? null;
            }
        }

        return $data;
    }

    /**
     * Optionally, modify data before saving to keep repeater data intact.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Modify or prevent changes to specific fields before saving if necessary.
        return $data;
    }

    /**
     * Custom handling of the record update process, if necessary.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Ensure the saving process respects repeater's independent state
        $record->update($data);

        return $record;
    }
}
