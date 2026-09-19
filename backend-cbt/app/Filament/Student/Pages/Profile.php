<?php

namespace App\Filament\Student\Pages;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Profil';

    protected static ?string $title = 'Profil Saya';

    protected static string $view = 'filament.student.pages.profile';

    public ?string $name = '';

    public ?string $email = '';

    public ?string $nisn = '';

    public ?string $nip = '';

    public ?string $phone = '';

    public ?string $gender = '';

    public ?string $address = '';

    public ?string $password = '';

    public ?string $password_confirmation = '';

    public ?bool $is_active = true;

    protected function getViewData(): array
    {
        $user = Auth::user();

        return [
            'user' => $user,
            'name' => $user->name,
            'email' => $user->email,
            'nisn' => $user->nisn,
            'nip' => $user->nip,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'address' => $user->address,
            'is_active' => $user->is_active,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP (untuk guru/staff)')
                            ->maxLength(20)
                            ->disabled(! Auth::user()->hasRole('guru') && ! Auth::user()->hasRole('admin')),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Keamanan Akun')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->label('Password Baru')
                            ->type('password')
                            ->password()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->type('password')
                            ->password()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function updateProfile(): void
    {
        $this->validate();

        $user = Auth::user();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'nisn' => $this->nisn,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ];

        // Only update password if provided
        if (! empty($this->password)) {
            if ($this->password !== $this->password_confirmation) {
                $this->addError('password_confirmation', 'Konfirmasi password tidak sesuai');

                return;
            }

            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->dispatch('profile-updated');

        session()->flash('message', 'Profil berhasil diperbarui.');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('updateProfile')
                ->label('Simpan Perubahan')
                ->submit('updateProfile')
                ->color('primary'),
        ];
    }
}
