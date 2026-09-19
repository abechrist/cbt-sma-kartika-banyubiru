<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RppResource\Pages;
use App\Filament\Resources\RppResource\RelationManagers\AssessmentsRelationManager;
use App\Filament\Resources\RppResource\RelationManagers\MaterialsRelationManager;
use App\Models\Rpp;
use App\Models\Subject;
use App\Services\RppIntegrationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RppResource extends Resource
{
    protected static ?string $model = Rpp::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Manajemen Akademik';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Import RPP dari JSON')
                    ->schema([
                        Forms\Components\FileUpload::make('rpp_json')
                            ->label('File JSON RPP')
                            ->acceptedFileTypes(['application/json'])
                            ->maxSize(1024)
                            ->helperText('Upload file JSON RPP yang dihasilkan dari sistem perancangan RPP')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state && $state->isValid()) {
                                    $content = file_get_contents($state->getRealPath());
                                    $data = json_decode($content, true);
                                    if ($data) {
                                        $metadata = $data['rpp_metadata'] ?? [];
                                        $set('subject_id', Subject::firstOrCreate(
                                            ['name' => $metadata['mata_pelajaran'] ?? ''],
                                            ['code' => strtoupper(substr($metadata['mata_pelajaran'] ?? '', 0, 3)), 'description' => '', 'is_active' => true]
                                        )->id);
                                        $set('topic', $metadata['topik_utama'] ?? '');
                                        $set('time_allocation', $metadata['alokasi_waktu'] ?? '');
                                        $set('academic_year', '2026/2027');
                                        $set('semester', 'Ganjil');
                                        $set('status', 'draft');
                                        $set('rpp_data', json_encode($data, JSON_PRETTY_PRINT));
                                    }
                                }
                            }),
                        Forms\Components\Hidden::make('rpp_data'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Informasi RPP')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('classGroup', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('teacher_id')
                            ->label('Guru')
                            ->relationship('teacher', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->required()
                            ->default('2026/2027'),
                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->default('Ganjil'),
                        Forms\Components\TextInput::make('topic')
                            ->label('Topik Utama')
                            ->required(),
                        Forms\Components\TextInput::make('time_allocation')
                            ->label('Alokasi Waktu')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'integrated' => 'Integrated',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\DateTimePicker::make('integrated_at')
                            ->label('Diintegrasikan Pada')
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('topic')
                    ->label('Topik')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classGroup.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('time_allocation')
                    ->label('Alokasi Waktu')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                        'info' => 'integrated',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('integrated_at')
                    ->label('Diintegrasikan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
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
                    ->relationship('subject', 'name'),
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->relationship('classGroup', 'name'),
                Tables\Filters\TernaryFilter::make('integrated_at')
                    ->label('Status Integrasi')
                    ->placeholder('Semua')
                    ->trueLabel('Terintegrasi')
                    ->falseLabel('Belum Terintegrasi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('integrated_at'),
                        false: fn (Builder $query) => $query->whereNull('integrated_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->modalWidth('4xl')
                    ->modalHeading('Detail RPP'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\Action::make('integrate')
                    ->label('Integrasikan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (Rpp $record) => $record->status !== 'integrated')
                    ->requiresConfirmation()
                    ->action(function (Rpp $record) {
                        $service = app(RppIntegrationService::class);
                        $service->importRpp($record->rpp_data);
                        $record->refresh();
                    })
                    ->after(function (Rpp $record) {
                        Notification::make()
                            ->title('RPP berhasil diintegrasikan')
                            ->body('Materi LMS dan Asesmen CBT telah dibuat.')
                            ->success()
                            ->send();
                    }),
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
            MaterialsRelationManager::class,
            AssessmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRpps::route('/'),
            'create' => Pages\CreateRpp::route('/create'),
            'edit' => Pages\EditRpp::route('/{record}/edit'),
            'view' => Pages\ViewRpp::route('/{record}'),
        ];
    }
}
