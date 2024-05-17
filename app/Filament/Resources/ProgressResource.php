<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgressResource\Pages;
use App\Models\Progress;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgressResource extends Resource
{
    protected static ?string $model = Progress::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'التقدم اليومي';
    protected static ?string $modelLabel = 'تقدم يومي';
    protected static ?string $pluralModelLabel = 'تقدم يومي';
    protected static ?string $recordTitleAttribute = 'date';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->label('الطالب')
                    ->searchable()
                    ->preload()
                    ->relationship('student', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('التاريخ')
                    ->default(now())
                    ->required(),
                Forms\Components\ToggleButtons::make('status')
                    ->label('الحالة')
                    ->inline()
                    ->icons([
                        'memorized' => 'heroicon-o-check-circle',
                        'absent' => 'heroicon-o-x-circle',
                    ])
                    ->grouped()
                    ->default('memorized')
                    ->colors([
                        'memorized' => 'primary',
                        'absent' => 'danger',
                    ])
                    ->options([
                        'memorized' => 'أتم الحفظ',
                        'absent' => 'غائب',
                    ])

                    ->required(),
                Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('page')
                            ->label('الصفحة')
                            ->options(range(1, 604))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('lines_from')
                            ->options(range(1, 15))
                            ->required(),
                        Forms\Components\Select::make('lines_to')
                            ->options(range(1, 15))
                            ->label('إلى السطر')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')->label('الطالب'),
                TextColumn::make('date')->label('التاريخ')->date(),
                TextColumn::make('status')->label('الحالة')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'memorized' => 'محفوظ',
                            'not_memorized' => 'غير محفوظ',
                            'absent' => 'غائب',
                        };
                    }),
                TextColumn::make('page')->label('الصفحة'),
                TextColumn::make('lines_from')->label('من السطر'),
                TextColumn::make('lines_to')->label('إلى السطر'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgress::route('/'),
            'create' => Pages\CreateProgress::route('/create'),
            'edit' => Pages\EditProgress::route('/{record}/edit'),
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'student.name',
            'date',
            'page',
            'lines_from',
            'lines_to',
        ];
    }
}
