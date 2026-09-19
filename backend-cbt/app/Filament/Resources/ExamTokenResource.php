<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamTokenResource\Pages;
use App\Models\ExamSession;
use App\Models\ExamToken;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamTokenResource extends Resource
{
    protected static ?string $model = ExamToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('exam_session_id')
                    ->label('Sesi Ujian')
                    ->options(ExamSession::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->disabled(! app(App\Enums\FilamentAdminRole::class->value) ?? true),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->required()
                    ->default(true),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Kadaluarsa')
                    ->required()
                    ->native(false),
                Forms\Components\Toggle::make('is_single_use')
                    ->label('Gunakan Satu Kali')
                    ->required()
                    ->default(true),
                Forms\Components\TextInput::make('used_count')
                    ->label('Jumlah Penggunaan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam_session.name')
                    ->label('Sesi Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\DateTimeColumn::make('expires_at')
                    ->label('Kadaluarsa')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_single_use')
                    ->label('Gunakan 1x')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Penggunaan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_session_id')
                    ->label('Sesi Ujian')
                    ->options(ExamSession::pluck('name', 'id'))
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListExamTokens::route('/'),
            'create' => Pages\CreateExamToken::route('/create'),
            'edit' => Pages\EditExamToken::route('/{record}/edit'),
        ];
    }
}
