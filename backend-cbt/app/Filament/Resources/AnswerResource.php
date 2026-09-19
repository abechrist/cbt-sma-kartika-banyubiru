<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnswerResource\Pages;
use App\Models\Answer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('exam_attempt_id')
                    ->label('Percobaan Ujian')
                    ->options(ExamAttempt::with(['user', 'exam_session.exam'])
                        ->get()
                        ->mapWithKeys(fn ($attempt) => [$attempt->id => $attempt->user->name.' - '.$attempt->exam_session->exam->name.' ('.$attempt->exam_session->name.')']))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('question_id')
                    ->label('Soal')
                    ->options(Question::pluck('question_text', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('answer_text')
                    ->label('Jawaban Teks')
                    ->columnSpanFull()
                    ->rows(3)
                    ->maxLength(1000),
                Forms\Components\Toggle::make('is_flagged')
                    ->label('Ditandai')
                    ->required()
                    ->default(false),
                Forms\Components\TextInput::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->default(0)
                    ->disabled(! auth()->user()?->isGuru() && ! auth()->user()?->isAdmin() && ! auth()->user()?->isSuperAdmin()),
                Forms\Components\DateTimePicker::make('answered_at')
                    ->label('Waktu Menjawab')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('graded_at')
                    ->label('Waktu Penilaian')
                    ->disabled(),
                Forms\Components\Select::make('graded_by')
                    ->label('Penilai')
                    ->options(User::whereIn('role.name', ['guru', 'admin', 'super_admin'])->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->disabled(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->rows(3)
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attempt.user.name')
                    ->label('Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question.question_text')
                    ->label('Soal')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('answer_text')
                    ->label('Jawaban')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_flagged')
                    ->label('Ditandai')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('answered_at')
                    ->label('Waktu Menjawab')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('graded_by')
                    ->label('Penilai')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_attempt_id')
                    ->label('Percobaan Ujian')
                    ->options(ExamAttempt::pluck('id', 'id'))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('question_id')
                    ->label('Soal')
                    ->options(Question::pluck('question_text', 'id'))
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_flagged')
                    ->label('Ditandai')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAnswers::route('/'),
            'create' => Pages\CreateAnswer::route('/create'),
            'edit' => Pages\EditAnswer::route('/{record}/edit'),
        ];
    }
}
