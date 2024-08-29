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
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($this->record->routine)) {
            $existingRoutine = json_decode($this->record->routine, true);
            foreach ($existingRoutine as $index => $existingEntry) {
                if (isset($data['routine'][$index])) {
                    $data['routine'][$index]['exam_id'] = $existingEntry['exam_id'];
                }
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }
}
