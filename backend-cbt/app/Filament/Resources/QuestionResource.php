<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use App\Models\StudentClass;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Manajemen Soal';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informasi Dasar')
                        ->schema([
                            Select::make('subject_id')
                                ->label('Mata Pelajaran')
                                ->options(Subject::pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->preload(),
                            Select::make('class_id')
                                ->label('Kelas')
                                ->options(StudentClass::pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->preload(),
                            Select::make('type')
                                ->label('Jenis Soal')
                                ->options([
                                    'pg' => 'Pilihan Ganda',
                                    'pg_kompleks' => 'Pilihan Ganda Kompleks',
                                    'benar_salah' => 'Benar Salah',
                                    'menjodohkan' => 'Menjodohkan',
                                    'isian_singkat' => 'Isian Singkat',
                                    'esai' => 'Esai',
                                ])
                                ->required()
                                ->native(false),
                            RichEditor::make('question_text')
                                ->label('Pertanyaan')
                                ->required()
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike', 'link', 'bulletList', 'orderedList',
                                ]),
                        ]),
                    Wizard\Step::make('Media & Nilai')
                        ->schema([
                            FileUpload::make('image_path')
                                ->label('Gambar')
                                ->image()
                                ->maxSize(1024)
                                ->imageEditor()
                                ->imageCropAspectRatio('16:9')
                                ->directory('question-images'),
                            FileUpload::make('audio_path')
                                ->label('Audio')
                                ->audio()
                                ->maxSize(1024)
                                ->directory('question-audio'),
                            FileUpload::make('video_path')
                                ->label('Video')
                                ->video()
                                ->maxSize(10240)
                                ->directory('question-video'),
                            Select::make('difficulty')
                                ->label('Tingkat Kesulitan')
                                ->options([
                                    'easy' => 'Mudah',
                                    'medium' => 'Sedang',
                                    'hard' => 'Sulit',
                                ])
                                ->required()
                                ->native(false),
                            TextInput::make('score')
                                ->label('Nilai')
                                ->required()
                                ->numeric()
                                ->default(1)
                                ->minValue(0.25)
                                ->maxValue(10)
                                ->step(0.25),
                            TextInput::make('competency_code')
                                ->label('Kode Kompetensi')
                                ->maxLength(50)
                                ->placeholder('Contoh: 3.1, 4.2'),
                        ])
                        ->columns(2),
                    Wizard\Step::make('Pengaturan')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->required()
                                ->default(true),
                        ])
                        ->columns(2),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('class.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pg' => 'info',
                        'pg_kompleks' => 'warning',
                        'benar_salah' => 'success',
                        'menjodohkan' => 'primary',
                        'isian_singkat' => 'secondary',
                        'esai' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('question_text')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->limit(50),
                ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('difficulty')
                    ->label('Tingkat Kesulitan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'easy' => 'success',
                        'medium' => 'warning',
                        'hard' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::pluck('name', 'id'))
                    ->multiple(),
                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->options(StudentClass::pluck('name', 'id'))
                    ->multiple(),
                SelectFilter::make('type')
                    ->label('Jenis Soal')
                    ->options([
                        'pg' => 'Pilihan Ganda',
                        'pg_kompleks' => 'Pilihan Ganda Kompleks',
                        'benar_salah' => 'Benar Salah',
                        'menjodohkan' => 'Menjodohkan',
                        'isian_singkat' => 'Isian Singkat',
                        'esai' => 'Esai',
                    ])
                    ->multiple(),
                SelectFilter::make('difficulty')
                    ->label('Tingkat Kesulitan')
                    ->options([
                        'easy' => 'Mudah',
                        'medium' => 'Sedang',
                        'hard' => 'Sulit',
                    ])
                    ->multiple(),
                TernaryFilter::make('is_active')
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
