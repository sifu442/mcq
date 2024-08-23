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
                    $this->refreshFormData([
                        'status',
                    ]);
                }),
            DeleteAction::make(),
        ];
    }
}
