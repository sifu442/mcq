<?php
namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Tables;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Forms\Components\CKEditor;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        $latestExam = Question::latest()->first();

        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([TextInput::make('name')->required()])
                ->preload()
                ->required()
                ->default($latestExam->subject_id ?? null),
            TextInput::make('last_appeared')
                ->label('Exam Name')
                ->default($latestExam->previous_exam ?? ''),
            TextInput::make('post')
                ->default($latestExam->post ?? ''),
            DatePicker::make('date')
                ->native(false)
                ->default($latestExam->date ?? '')
                ->firstDayOfWeek(6),
            TextInput::make('topic')
                ->default($latestExam->topic ?? ''),
            CKEditor::make('title')->required()->columnSpanFull()->default($latestExam->title ?? ''),
            Section::make('Options')
                ->schema([
                    CKEditor::make('option_a')->required()->columnSpanFull()->default($latestExam->otpion_a ?? ''),
                    CKEditor::make('option_b')->required()->columnSpanFull()->default($latestExam->otpion_b ?? ''),
                    CKEditor::make('option_c')->required()->columnSpanFull()->default($latestExam->otpion_c ?? ''),
                    CKEditor::make('option_d')->required()->columnSpanFull()->default($latestExam->otpion_d ?? ''),
                    Select::make('right_answer')
                        ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                    ]),
                ]),
            CKEditor::make('explanation')->columnSpanFull()->default($latestExam->explanation ?? ''),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->getStateUsing(function ($rowLoop, $livewire): string {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                TextColumn::make('title')->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state)),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('attach')
                    ->label('Attach or Create Question ')
                    ->url(fn () => url("/admin/exams/{$this->ownerRecord->id}/attach"))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

}
