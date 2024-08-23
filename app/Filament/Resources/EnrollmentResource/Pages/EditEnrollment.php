<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
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

    public function adjustDates(array $data)
    {
        $days = (int) $data['days'];

        if ($days > 0) {
            $enrollment = $this->record;

            foreach ($enrollment->routine as $routine) {
                $routine->start_time = Carbon::parse($routine->start_time)->addDays($days);
                $routine->end_time = Carbon::parse($routine->end_time)->addDays($days);
                $routine->save();
            }

            $this->notify('success', 'Dates have been adjusted successfully.');
        } else {
            $this->notify('warning', 'Please enter a valid number of days.');
        }
    }
}
