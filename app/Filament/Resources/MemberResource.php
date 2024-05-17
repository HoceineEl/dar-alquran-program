<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationLabel = 'الطلاب';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'طالب';
    protected static ?string $pluralModelLabel = 'طلاب';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('نوع الحفظ')
                    ->options([
                        'two_lines' => 'سطران',
                        'half_page' => 'نصف صفحة',
                    ])
                    ->default('two_lines')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->default('06')
                    ->required(),
                Forms\Components\Select::make('group')
                    ->options([
                        '1' => 'المجموعة 1',
                        '2' => 'المجموعة 2',
                        '3' => 'المجموعة 3',
                        '4' => 'المجموعة 4',
                        '5' => 'المجموعة 5',
                        '6' => 'المجموعة 6',
                    ])
                    ->label('المجموعة')
                    ->default(1),
                Forms\Components\Select::make('sex')
                    ->label('الجنس')
                    ->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    ])
                    ->default('male'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم'),
                TextColumn::make('type')->label('نوع الحفظ')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'two_lines' => 'سطران',
                            'half_page' => 'نصف صفحة',
                        };
                    }),
                TextColumn::make('phone')->label('رقم الهاتف'),
                TextColumn::make('group')->label('المجموعة')
                    ->icon('heroicon-o-users')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => 'المجموعة 1',
                            2 => 'المجموعة 2',
                            3 => 'المجموعة 3',
                            4 => 'المجموعة 4',
                            5 => 'المجموعة 5',
                            6 => 'المجموعة 6',
                        };
                    }),
                TextColumn::make('sex')->label('الجنس')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'male' => 'ذكر',
                            'female' => 'أنثى',
                        };
                    }),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone'];
    }
}
