<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentClassResource\Pages;
use App\Models\StudentClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Manajemen Akademik';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kelas')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: XI IPS 1'),
                        Forms\Components\Select::make('grade')
                            ->label('Tingkat')
                            ->options([
                                '10' => 'X',
                                '11' => 'XI',
                                '12' => 'XII',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Ajaran')
                            ->required()
                            ->maxLength(20)
                            ->default(date('Y').'/'.(date('Y') + 1)),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->required()
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Tingkat')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Tingkat')
                    ->options([
                        '10' => 'X',
                        '11' => 'XI',
                        '12' => 'XII',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('grade', 'desc')
            ->reorderable();
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
            'index' => Pages\ListStudentClasses::route('/'),
            'create' => Pages\CreateStudentClass::route('/create'),
            'edit' => Pages\EditStudentClass::route('/{record}/edit'),
        ];
    }
}
