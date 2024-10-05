<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Carbon\Carbon;
use App\Models\Course;
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
                ->form([TextInput::make('days')->label('Increase/Decrease Days')->numeric()->required()])
                ->action(function (array $data) {
                    $days = $data['days'];

                    $this->record->routine = collect($this->record->routine)
                        ->map(function ($item, $index) use ($days) {
                            if ($index > 0) {
                                $item['start_time'] = Carbon::parse($item['start_time'])
                                    ->addDays($days)
                                    ->format('Y-m-d H:i:s');
                                $item['end_time'] = Carbon::parse($item['end_time'])
                                    ->addDays($days)
                                    ->format('Y-m-d H:i:s');
                            }
                            return $item;
                        })
                        ->toArray();

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
    // Check if the routine exists in the current record
    if (isset($this->record->routine) && is_array($this->record->routine)) {
        $existingRoutine = $this->record->routine;

        // Get enrolled_at date
        $enrolledAt = Carbon::parse($this->record->enrolled_at);

        // Get the selected exam ID from the 'starts_from' field
        $selectedExamId = $data['starts_from'];

        // Fetch the course to get the gap value
        $course = Course::find($this->record->course_id);
        $gap = $course->gap ?? 0; // Default to 0 if no gap is found

        // Initialize variables for tracking adjustments
        $adjustBefore = true;
        $adjustAfter = false;
        $previousDay = $enrolledAt->copy()->subDay(); // Start with the day before enrolled_at
        $nextStartTime = $enrolledAt->copy(); // We will calculate the next start_time based on the selected exam's end_time

        // Loop through the routine and adjust the dates for exams
        foreach ($existingRoutine as $index => $existingEntry) {
            // Preserve the existing exam_id in the routine data
            if (isset($data['routine'][$index])) {
                // Keep the exam_id unchanged
                $data['routine'][$index]['exam_id'] = $existingEntry['exam_id'];

                // Adjust the exams that come before the selected starts_from
                if ($adjustBefore && $existingEntry['exam_id'] != $selectedExamId) {
                    // Set the previous exams' dates to be before the enrolled_at date
                    $data['routine'][$index]['start_time'] = $previousDay->copy()->format('Y-m-d H:i:s');
                    $data['routine'][$index]['end_time'] = $previousDay->copy()->addDay()->format('Y-m-d H:i:s');

                    // Move to the day before for the next exam
                    $previousDay->subDay();
                } elseif ($existingEntry['exam_id'] == $selectedExamId) {
                    // When we find the selected starts_from exam, update its dates relative to enrolled_at
                    $startTime = $enrolledAt->copy()->addDay();
                    $endTime = $startTime->copy()->addDay(); // Adjust end_time as needed

                    $data['routine'][$index]['start_time'] = $startTime->format('Y-m-d H:i:s');
                    $data['routine'][$index]['end_time'] = $endTime->format('Y-m-d H:i:s');

                    // Stop adjusting for previous exams and start adjusting future ones
                    $adjustBefore = false;
                    $adjustAfter = true;

                    // Prepare the next start time based on the current exam's end_time
                    $nextStartTime = $endTime->copy(); // Start after the selected exam's end_time
                } elseif ($adjustAfter) {
                    // Adjust exams after starts_from based on the gap
                    $nextStartTime = $nextStartTime->addDays($gap); // Add the gap days for the next exam
                    $nextEndTime = $nextStartTime->copy()->addDay(); // Adjust end_time for next exam

                    $data['routine'][$index]['start_time'] = $nextStartTime->format('Y-m-d H:i:s');
                    $data['routine'][$index]['end_time'] = $nextEndTime->format('Y-m-d H:i:s');
                }
            }
        }
    }

    return $data;
}

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $record->getKey()]));
        return $record;
    }
}
