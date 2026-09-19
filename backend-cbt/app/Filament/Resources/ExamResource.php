<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Models\Exam;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Ujian')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Ujian')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Ujian PTS Matematika Kelas XI'),
                        Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->options(Subject::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi (menit)')
                            ->numeric()
                            ->required()
                            ->default(120)
                            ->minValue(15)
                            ->maxValue(300),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Pengaturan Soal')
                    ->schema([
                        Forms\Components\Toggle::make('randomize_questions')
                            ->label('Acak Soal')
                            ->required()
                            ->default(false),
                        Forms\Components\Toggle::make('randomize_options')
                            ->label('Acak Opsi Jawaban')
                            ->required()
                            ->default(false),
                        Forms\Components\Toggle::make('allow_back')
                            ->label('Boleh Kembali')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('show_result_after')
                            ->label('Tampilkan Hasil Setelah Selesai')
                            ->required()
                            ->default(false),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Diterbitkan',
                                'active' => 'Aktif',
                                'archived' => 'Diarsipkan',
                            ])
                            ->required()
                            ->default('draft')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' menit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'info',
                        'active' => 'success',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('randomize_questions')
                    ->label('Acak Soal')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('randomize_options')
                    ->label('Acak Opsi')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::pluck('name', 'id'))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Diterbitkan',
                        'active' => 'Aktif',
                        'archived' => 'Diarsipkan',
                    ])
                    ->multiple(),
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
