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
                ->action('adjustDates')
                ->color('primary'),
            Actions\DeleteAction::make(),
        ];
    }

    public function adjustDates()
    {
        $data = $this->form->getState();

        if (isset($data['days'])) {
            $days = (int) $data['days'];

            if ($days > 0) {
                $enrollment = $this->record;

                foreach ($enrollment->routine as $routine) {
                    $newStartTime = Carbon::parse($routine->start_time)->addDays($days);
                    $newEndTime = Carbon::parse($routine->end_time)->addDays($days);

                    // Update the database
                    $routine->update([
                        'start_time' => $newStartTime,
                        'end_time' => $newEndTime,
                    ]);
                }

                // Refresh the form to reflect the changes
                $this->fillForm();
            }
        }
    }

}
