<?php

namespace App\Filament\Resources\RppResource\RelationManagers;

use App\Models\RppAssessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    protected static ?string $title = 'Integrasi ke CBT (Asesmen)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Tujuan Pembelajaran')
                    ->schema([
                        Forms\Components\TextInput::make('tp_id')
                            ->label('ID TP')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('deskripsi_tp')
                            ->label('Deskripsi Tujuan Pembelajaran')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Section::make('Konfigurasi Asesmen')
                            ->schema([
                                Forms\Components\TextInput::make('jumlah_soal_direkomendasikan')
                                    ->label('Jumlah Soal Direkomendasikan')
                                    ->numeric()
                                    ->default(5)
                                    ->required(),
                                Forms\Components\Select::make('tipe_soal')
                                    ->label('Tipe Soal')
                                    ->options([
                                        'Pilihan Ganda' => 'Pilihan Ganda',
                                        'Esai' => 'Esai',
                                        'Isian Singkat' => 'Isian Singkat',
                                        'Benar/Salah' => 'Benar/Salah',
                                    ])
                                    ->required()
                                    ->default('Pilihan Ganda'),
                                Forms\Components\Select::make('tingkat_kesulitan')
                                    ->label('Tingkat Kesulitan')
                                    ->options([
                                        'Mudah' => 'Mudah',
                                        'Sedang' => 'Sedang',
                                        'Sukar' => 'Sukar',
                                    ])
                                    ->required()
                                    ->default('Sedang'),
                                Forms\Components\TextInput::make('kata_kunci_indokator_soal')
                                    ->label('Kata Kunci / Indikator Soal')
                                    ->helperText('Kata kunci untuk membuat soal CBT, pisahkan dengan koma')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tp_id')
            ->columns([
                Tables\Columns\TextColumn::make('tp_id')
                    ->label('ID TP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi_tp')
                    ->label('Deskripsi TP')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('jumlah_soal_direkomendasikan')
                    ->label('Jumlah Soal')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tipe_soal')
                    ->label('Tipe Soal')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tingkat_kesulitan')
                    ->label('Tingkat Kesulitan')
                    ->colors([
                        'success' => 'Mudah',
                        'warning' => 'Sedang',
                        'danger' => 'Sukar',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam.name')
                    ->label('Exam Terkait')
                    ->getStateUsing(fn (RppAssessment $record) => $record->exam?->name ?? 'Belum dibuat')
                    ->color(fn (RppAssessment $record) => $record->exam ? 'success' : 'warning')
                    ->icon(fn (RppAssessment $record) => $record->exam ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_soal')
                    ->label('Tipe Soal')
                    ->options([
                        'Pilihan Ganda' => 'Pilihan Ganda',
                        'Esai' => 'Esai',
                        'Isian Singkat' => 'Isian Singkat',
                        'Benar/Salah' => 'Benar/Salah',
                    ]),
                Tables\Filters\SelectFilter::make('tingkat_kesulitan')
                    ->label('Tingkat Kesulitan')
                    ->options([
                        'Mudah' => 'Mudah',
                        'Sedang' => 'Sedang',
                        'Sukar' => 'Sukar',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('createExam')
                    ->label('Buat Exam CBT')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn (RppAssessment $record) => route('filament.admin.resources.exams.create', [
                        'subject_id' => $record->rpp->subject_id,
                    ]))
                    ->visible(fn (RppAssessment $record) => ! $record->exam),
                Tables\Actions\Action::make('linkExam')
                    ->label('Hubungkan dengan Exam CBT')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->url(fn (RppAssessment $record) => route('filament.admin.resources.exams.index'))
                    ->visible(fn (RppAssessment $record) => ! $record->exam),
                Tables\Actions\ViewAction::make()
                    ->visible(fn (RppAssessment $record) => (bool) $record->exam)
                    ->url(fn (RppAssessment $record) => route('filament.admin.resources.exams.edit', [
                        'record' => $record->exam->id,
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
