<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Forms\Components\CKEditor;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ReplicateAction;
use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\ExamsRelationManager;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    //protected static ?string $modelLabel = 'কোর্স';
    //protected static ?string $pluralModelLabel = 'কোর্সগুলো';

    protected static ?string $navigationGroup = 'Course Content';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $crossIcon = 'gmdi-do-not-disturb-alt';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, $state) {
                    $set('slug', Str::slug($state));
                }),
            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->disabledOn('edit')
                ->maxLength(255),
            TextInput::make('time_span')
                ->required()
                ->numeric()
                ->suffix('days'),
            TextInput::make('price')
                ->translateLabel()
                ->numeric()
                ->prefix('৳')
                ->maxValue(42949672.95)
                ->hidden(fn(callable $get) => $get('is_free'))
                ->required(),
            TextInput::make('total_exams')
                ->translateLabel()
                ->numeric()
                ->required(),
            TextInput::make('discounted_price')
                ->translateLabel()
                ->numeric()
                ->prefix('৳')
                ->maxValue(42949672.95)
                ->required(),
            TextInput::make('gap')
                ->required()
                ->numeric()
                ->suffix('Days')
                ->translateLabel()
                ->default(3),
            TextInput::make('participation_time')
                ->required()
                ->numeric()
                ->suffix('Hours')
                ->default(24)
                ->translateLabel(),
            Toggle::make('featured')
                ->onIcon('heroicon-m-bolt')
                ->offIcon('heroicon-m-user'),
            CKEditor::make('description')->required()->columnSpanFull(),
            Select::make('category')
                ->options([
                    'job_solution' => 'Job Solution',
                    'service_wise' => 'Service Wise',
                    'subject_wise' => 'Subject Wise',
                    'gap_wise' => 'Subject Wise',
                    'free' => 'Free',
                ])
                ->native(false),
            FileUpload::make('attachment')->multiple(),
            CKEditor::make('routine_heading')->required()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->state(static function (HasTable $livewire, stdClass $rowLoop): string
                    {return (string) ($rowLoop->iteration + $livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1));
                }),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('exams_count')
                    ->label('Number of Exams')
                    ->getStateUsing(function (Course $record) {
                        return $record->exams()->count();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('clone')
                    ->label('Clone')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->color('info')
                    ->action(function (Course $record) {
                        DB::transaction(function () use ($record) {
                            // Clone the course
                            $clonedCourse = $record->replicate();
                            $clonedCourse->title = $record->title . ' (Copy)';
                            $clonedCourse->slug = Str::slug($record->slug . '-copy');
                            $clonedCourse->save();

                            // Clone associated exams
                            foreach ($record->exams as $exam) {
                                $clonedCourse->exams()->attach($exam->id);
                            }
                        });
                    }),
                DeleteAction::make()
                    ->requiresConfirmation(),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [ExamsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
