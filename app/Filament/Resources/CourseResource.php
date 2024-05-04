<?php

namespace App\Filament\Resources;

use tidy;
use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CourseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Filament\Resources\CourseResource\RelationManagers\ExamsRelationManager;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;


    protected static ?string $modelLabel = 'কোর্স';
    protected static ?string $pluralModelLabel = 'কোর্সগুলো';

    //protected static ?string $modelLabel = 'কোর্স';
    //protected static ?string $pluralModelLabel = 'কোর্সগুলো';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
            ->live()
            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                ->required(),

            TextInput::make('slug')->required(),
            TextInput::make('time_span')->required()
            ->numeric()->suffix('days'),


            DatePicker::make('published_at')
                ->native(false)
                ->minDate(now()->subYears(2))
                ->maxDate(now()),

            Toggle::make('is_free')
                ->label('Free')
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('price', 0)),

            DatePicker::make('published_at'),



            TextInput::make('price')
                ->translateLabel()
                ->numeric()
                ->prefix('৳')
                ->maxValue(42949672.95)
                ->hidden(fn (callable $get) => $get('is_free'))
                ->required(),



            TextInput::make('total_exams')
                ->translateLabel()
                ->numeric()
                ->required(),


            TextInput::make('deducted_price')
                ->translateLabel()
                ->numeric()
                ->prefix('৳')
                ->maxValue(42949672.95),

            Toggle::make('featured')
                ->onIcon('heroicon-m-bolt')
                ->offIcon('heroicon-m-user'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                TextColumn::make('title')
                ])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
                ExamsRelationManager::class
            ];
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