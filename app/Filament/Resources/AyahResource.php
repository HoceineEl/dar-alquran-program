<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AyahResource\Pages;
use App\Models\Ayah;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AyahResource extends Resource
{
    protected static ?string $model = Ayah::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'الصفحات';

    protected static ?string $modelLabel = 'صفحة';

    protected static ?string $pluralModelLabel = 'الصفحات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('surah_name')
                    ->content(fn ($record) => $record->surah_name)
                    ->label('اسم السورة'),
                Forms\Components\Placeholder::make('ayah_text')
                    ->content(fn ($record) => $record->ayah_text)
                    ->label('نص الآية'),
                Forms\Components\TextInput::make('page_number')
                    ->label('رقم الصفحة')
                    ->required(),
                Forms\Components\TextInput::make('line_start')
                    ->label('سطر البداية')
                    ->required(),
                Forms\Components\TextInput::make('line_end')
                    ->label('سطر النهاية')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ayah_text')
                    ->searchable()
                    ->label('نص الآية')
                    ->fontFamily('Amiri Quran')
                    ->sortable(),
                TextColumn::make('surah_name')
                    ->searchable()
                    ->label('اسم السورة')
                    ->fontFamily('Amiri Quran')
                    ->sortable(),
                TextColumn::make('page_number')
                    ->searchable()
                    ->label('رقم الصفحة')
                    ->sortable(),
                TextColumn::make('line_start')
                    ->searchable()
                    ->label('سطر البداية')
                    ->sortable(),
                TextColumn::make('line_end')
                    ->searchable()
                    ->label('سطر النهاية')
                    ->sortable(),
                TextColumn::make('lines_count')
                    ->searchable()
                    ->label('عدد الأسطر')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAyahs::route('/'),
            'create' => Pages\CreateAyah::route('/create'),
            'edit' => Pages\EditAyah::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'page_number',
            'ayah_text',
            'line_start',
            'line_end',
            'surah_name',
        ];
    }
}
