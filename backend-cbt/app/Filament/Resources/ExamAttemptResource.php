<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamAttemptResource\Pages;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamAttemptResource extends Resource
{
    protected static ?string $model = ExamAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peserta')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Peserta')
                            ->options(User::whereIn('role.name', ['siswa'])->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('exam_session_id')
                            ->label('Sesi Ujian')
                            ->options(ExamSession::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Waktu & Status')
                    ->schema([
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Waktu Mulai')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('ended_at')
                            ->label('Waktu Selesai')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->label('Waktu Submit')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'not_started' => 'Belum Dimulai',
                                'in_progress' => 'Sedang Berlangsung',
                                'submitted' => 'Teliti',
                                'auto_submitted' => 'Auto Submit',
                                'expired' => 'Kadaluarsa',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->disabled()
                            ->native(false),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Detail Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('current_question_order')
                            ->label('Nomor Soal Saat Ini')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\Toggle::make('is_resumed')
                            ->label('Diulang')
                            ->boolean()
                            ->disabled(),
                        Forms\Components\Toggle::make('suspicious_flags')
                            ->label('Flag Mencurigakan')
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam_session.name')
                    ->label('Sesi Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'not_started' => 'secondary',
                        'in_progress' => 'warning',
                        'submitted' => 'success',
                        'auto_submitted' => 'info',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submit')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_resumed')
                    ->label('Diulang')
                    ->boolean()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'not_started' => 'Belum Dimulai',
                        'in_progress' => 'Sedang Berlangsung',
                        'submitted' => 'Teliti',
                        'auto_submitted' => 'Auto Submit',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),
            ])
            ->actions([
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
            'index' => Pages\ListExamAttempts::route('/'),
            'create' => Pages\CreateExamAttempt::route('/create'),
            'edit' => Pages\EditExamAttempt::route('/{record}/edit'),
        ];
    }
}
