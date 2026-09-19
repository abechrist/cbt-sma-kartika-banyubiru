<?php

namespace App\Filament\Resources\RppResource\RelationManagers;

use App\Models\RppMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $title = 'Integrasi ke LMS (Materi Pembelajaran)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Materi LMS')
                    ->schema([
                        Forms\Components\TextInput::make('pertemuan_ke')
                            ->label('Pertemuan Ke')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('judul_topik')
                            ->label('Judul Topik')
                            ->required(),
                        Forms\Components\Textarea::make('deskripsi_aktivitas')
                            ->label('Deskripsi Aktivitas')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('rekomendasi_bahan_ajar')
                            ->label('Rekomendasi Bahan Ajar')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Nama Bahan Ajar')
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_topik')
            ->columns([
                Tables\Columns\TextColumn::make('pertemuan_ke')
                    ->label('Pertemuan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul_topik')
                    ->label('Judul Topik')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('learningMaterial.title')
                    ->label('Learning Material')
                    ->getStateUsing(fn (RppMaterial $record) => $record->learningMaterial?->title ?? 'Belum dibuat')
                    ->color(fn (RppMaterial $record) => $record->learningMaterial ? 'success' : 'warning')
                    ->icon(fn (RppMaterial $record) => $record->learningMaterial ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('createMaterial')
                    ->label('Buat Materi LMS')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn (RppMaterial $record) => route('filament.admin.resources.learning-materials.create', [
                        'course_id' => $record->rpp->course_id ?? null,
                    ]))
                    ->visible(fn (RppMaterial $record) => ! $record->learningMaterial),
                Tables\Actions\Action::make('linkMaterial')
                    ->label('Hubungkan dengan Materi LMS')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->url(fn (RppMaterial $record) => route('filament.admin.resources.learning-materials.index'))
                    ->visible(fn (RppMaterial $record) => ! $record->learningMaterial),
                Tables\Actions\ViewAction::make()
                    ->visible(fn (RppMaterial $record) => (bool) $record->learningMaterial)
                    ->url(fn (RppMaterial $record) => route('filament.admin.resources.learning-materials.edit', [
                        'record' => $record->learningMaterial->id,
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
