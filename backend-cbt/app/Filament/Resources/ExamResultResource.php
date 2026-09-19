<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResultResource\Pages;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamResultResource extends Resource
{
    protected static ?string $model = ExamResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Hasil')
                    ->schema([
                        Forms\Components\Select::make('exam_attempt_id')
                            ->label('Percobaan Ujian')
                            ->options(ExamAttempt::with(['user', 'exam_session.exam'])
                                ->get()
                                ->mapWithKeys(fn ($attempt) => [$attempt->id => $attempt->user->name.' - '.$attempt->exam_session->exam->name.' ('.$attempt->exam_session->name.')']))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('total_score')
                            ->label('Skor Total')
                            ->numeric()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('max_possible_score')
                            ->label('Skor Maksimum')
                            ->numeric()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('percentage')
                            ->label('Persentase')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->suffix('%'),
                        Forms\Components\TextInput::make('correct_count')
                            ->label('Jawaban Benar')
                            ->numeric()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('incorrect_count')
                            ->label('Jawaban Salah')
                            ->numeric()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('unanswered_count')
                            ->label('Tidak Dijawab')
                            ->numeric()
                            ->required()
                            ->disabled(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Grading')
                    ->schema([
                        Forms\Components\Select::make('grading_status')
                            ->label('Status Penilaian')
                            ->options([
                                'pending' => 'Menunggu Penilaian',
                                'graded' => 'Dinilai',
                                'needs_review' => 'Perlu Ditinjau Ulang',
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false),
                        Forms\Components\DateTimePicker::make('graded_at')
                            ->label('Waktu Penilaian')
                            ->disabled(),
                        Forms\Components\Select::make('graded_by')
                            ->label('Penilai')
                            ->options(User::whereIn('role.name', ['guru', 'admin', 'super_admin'])->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam_attempt.user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam_attempt.exam_session.exam.name')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam_attempt.exam_session.name')
                    ->label('Sesi Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Nilai (%)')
                    ->numeric()
                    ->sortable()
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Skor Mendapat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_possible_score')
                    ->label('Skor Maksimum')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('correct_count')
                    ->label('Benar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('incorrect_count')
                    ->label('Salah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unanswered_count')
                    ->label('Tidak Dijawab')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grading_status')
                    ->label('Status Penilaian')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'graded' => 'success',
                        'needs_review' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('graded_at')
                    ->label('Waktu Penilaian')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grading_status')
                    ->label('Status Penilaian')
                    ->options([
                        'pending' => 'Menunggu Penilaian',
                        'graded' => 'Dinilai',
                        'needs_review' => 'Perlu Ditinjau Ulang',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('exam_attempt.exam_session.exam_id')
                    ->label('Ujian')
                    ->relationship('exam_attempt.exam_session', 'exam.name')
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
            'index' => Pages\ListExamResults::route('/'),
            'create' => Pages\CreateExamResult::route('/create'),
            'edit' => Pages\EditExamResult::route('/{record}/edit'),
        ];
    }
}
