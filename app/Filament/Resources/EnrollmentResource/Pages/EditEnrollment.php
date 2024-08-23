<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EnrollmentResource;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Apply Days')
                ->label('Apply Days to Routine')
                ->form([
                    TextInput::make('days')
                        ->label('Number of Days')
                        ->numeric()
                        ->required(),
                ])
                ->action(function () {
                    $data = $this->form->getState();

                    if (isset($data['days'])) {
                        $days = (int) $data['days'];

                        if ($days > 0) {
                            $enrollment = $this->record;

                            foreach ($enrollment->routine as $routineData) {
                                $routine = json_decode($routineData, true); // Convert JSON to array

                                $startTime = Carbon::parse($routine['start_time']);
                                $endTime = Carbon::parse($routine['end_time']);

                                $newStartTime = $startTime->addDays($days);
                                $newEndTime = $endTime->addDays($days);

                                // Update the routine in the database
                                // Assuming you have a method to update the routine, otherwise, you may need to handle this based on your model's setup
                                $routine->update([
                                    'start_time' => $newStartTime,
                                    'end_time' => $newEndTime,
                                ]);
                            }

                            // Refresh the form
                            $this->fillForm();
                        }
                    }
                })
                ->color('primary')
                ->modalHeading('Adjust Dates by Days')
                ->requiresConfirmation(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If `days` is present in the form data, adjust the dates
        if (isset($data['days']) && is_numeric($data['days'])) {
            $days = (int) $data['days'];

            // Adjust `routine` dates
            if (isset($data['routine']) && is_array($data['routine'])) {
                foreach ($data['routine'] as &$routine) {
                    $startTime = Carbon::parse($routine['start_time']);
                    $endTime = Carbon::parse($routine['end_time']);

                    $routine['start_time'] = $startTime->addDays($days)->toDateTimeString();
                    $routine['end_time'] = $endTime->addDays($days)->toDateTimeString();
                }
            }
        }

        return $data;
    }
}
