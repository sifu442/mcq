<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\EnrollmentResource;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Apply Days')
                ->label('Apply Days to Routine')
                ->action('applyDaysToRoutine')
                ->form([
                    
                ])
                ->requiresConfirmation()
                ->color('primary'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('day_number')
                ->label('Number of Days')
                ->numeric(),
            Repeater::make('routine')
                ->schema([
                    DatePicker::make('start_time')
                        ->label('Start Time'),
                    DatePicker::make('end_time')
                        ->label('End Time'),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public function applyDaysToRoutine()
    {
        $data = $this->form->getState();

        $days = (int)($data['day_number'] ?? 0);

        if ($days > 0 && isset($data['routine'])) {
            foreach ($data['routine'] as $index => $routine) {
                if (isset($routine['start_time']) && isset($routine['end_time'])) {
                    $newStartTime = Carbon::parse($routine['start_time'])->addDays($days);
                    $newEndTime = Carbon::parse($routine['end_time'])->addDays($days);

                    $this->form->fill([
                        "routine.{$index}.start_time" => $newStartTime,
                        "routine.{$index}.end_time" => $newEndTime,
                    ]);
                }
            }

            // Save the updated state
            $this->save();

            Notification::make()
                ->title('Days Applied Successfully')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Please enter a valid number of days.')
                ->warning()
                ->send();
        }
    }
}
