<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSessionResource\Pages;
use App\Models\Exam;
use App\Models\ExamSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sesi Ujian')
                    ->schema([
                        Forms\Components\Select::make('exam_id')
                            ->label('Ujian')
                            ->options(Exam::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Sesi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Sesi 1 - Rabu, 03 September 2026'),
                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Waktu Mulai')
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('end_at')
                            ->label('Waktu Selesai')
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('room')
                            ->label('Ruangan')
                            ->maxLength(100)
                            ->placeholder('Contoh: Lab Komputer 1'),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Maksimal Peserta')
                            ->numeric()
                            ->required()
                            ->default(40)
                            ->minValue(1),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Pengaturan Token')
                    ->schema([
                        Forms\Components\TextInput::make('token_prefix')
                            ->label('Prefiks Token')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('Contoh: PTS-MAT')
                            ->help('Token akan digenerate sebagai: PREFIX-XXXXXX'),
                    ]),
                Forms\Components\Section::make('Instruksi & Pengaturan')
                    ->schema([
                        Forms\Components\Textarea::make('instructions')
                            ->label('Instruksi untuk Peserta')
                            ->columnSpanFull()
                            ->rows(4)
                            ->placeholder('Contoh: Dilarang menggunakan kalkulator, buka hanya sekurang-kurangnya 10 menit sebelum ujian dimulai...'),
                        Forms\Components\Toggle::make('allow_resume')
                            ->label('Izinkan Melanjutkan Ujian')
                            ->required()
                            ->default(false)
                            ->help('Peserta dapat melanjutkan ujian jika terputus selama masih dalam batas waktu'),
                        Forms\Components\Toggle::make('auto_submit_on_timeout')
                            ->label('Auto Submit Ketika Waktu Habis')
                            ->required()
                            ->default(true)
                            ->help('Jawaban akan otomatis disubmit ketika waktu ujian habis'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Sesi')
                            ->options([
                                'scheduled' => 'Terjadwal',
                                'open' => 'Dibuka',
                                'in_progress' => 'Sedang Berlangsung',
                                'completed' => 'Selesai',
                                'finished' => 'Final',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('scheduled')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Sesi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room')
                    ->label('Ruangan')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Maks. Peserta')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'open' => 'info',
                        'in_progress' => 'success',
                        'completed' => 'gray',
                        'finished' => 'danger',
                        'cancelled' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('token_prefix')
                    ->label('Prefiks Token')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('allow_resume')
                    ->label('Boleh Lanjut')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->label('Ujian')
                    ->options(Exam::pluck('name', 'id'))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'scheduled' => 'Terjadwal',
                        'open' => 'Dibuka',
                        'in_progress' => 'Sedang Berlangsung',
                        'completed' => 'Selesai',
                        'finished' => 'Final',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('allow_resume')
                    ->label('Izinkan Melanjutkan')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
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
            ->defaultSort('start_at', 'desc');
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
            'index' => Pages\ListExamSessions::route('/'),
            'create' => Pages\CreateExamSession::route('/create'),
            'edit' => Pages\EditExamSession::route('/{record}/edit'),
        ];
    }
}
