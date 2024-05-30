<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgressResource\Pages\CreateProgress;
use App\Filament\Resources\ProgressResource\Pages\EditProgress;
use App\Filament\Resources\ProgressResource\Pages\ListProgress;
use App\Helpers\ProgressFormHelper;
use App\Models\Progress;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
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
            ->schema(
                ProgressFormHelper::getProgressFormSchema(),
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')->label('الطالب')->sortable(),
                TextColumn::make('date')->label('التاريخ')->date(),
                TextColumn::make('status')->label('الحالة')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'memorized' => 'محفوظ',
                            'not_memorized' => 'غير محفوظ',
                            'absent' => 'غائب',
                        };
                    })->sortable(),
                TextColumn::make('page.number')->label('الصفحة')->sortable(),
                TextColumn::make('lines_from')
                    ->getStateUsing(fn ($record) => $record->lines_from + 1)
                    ->label('من السطر'),
                TextColumn::make('lines_to')
                    ->getStateUsing(fn ($record) => $record->lines_to + 1)
                    ->label('إلى السطر'),
                TextColumn::make('page.surah_name')->label('اسم السورة')->toggleable()->sortable(),
                SelectColumn::make('comment')
                    ->options([
                        'message_sent' => 'تم إرسال الرسالة',
                        'call_made' => 'تم الاتصال',
                    ])
                    ->label('التعليق')->toggleable(),
                TextColumn::make('createdBy.name')->label('أضيف بواسطة')->sortable(),
                TextColumn::make('created_at')->label('تاريخ الإضافة')->date(),
            ])
            ->searchable()
            ->filters([
                SelectFilter::make('date')
                    ->label('التاريخ')
                    ->options(fn () => Progress::query()->select('date')->distinct()->get()->pluck('date', 'date')->toArray())
                    ->default(now()->format('Y-m-d')),
                SelectFilter::make('group')
                    ->label('المجموعة')
                    ->relationship('student.group', 'name')
            ], FiltersLayout::AboveContent)
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
            'index' => ListProgress::route('/'),
            'create' => CreateProgress::route('/create'),
            'edit' => EditProgress::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'student.name',
            'date',
            'ayah',
            'lines_from',
            'lines_to',
        ];
    }
}
