<?php

namespace App\Filament\Psikolog\Resources\PsikologProfiles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PsikologProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // =========================
            // INFORMASI PRIBADI
            // =========================
            Section::make('Informasi Pribadi')
                ->schema([
                    Grid::make(1)->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),

                        TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->maxLength(25),

                        Grid::make(2)->schema([
                            TextInput::make('place_of_birth')->label('Tempat Lahir')->required(),
                            DatePicker::make('date_of_birth')->label('Tanggal Lahir')->required()->native(false),
                        ]),

                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),

                        TextInput::make('last_education')->label('Pendidikan Terakhir'),

                        TextInput::make('phone')->label('Nomor Telepon')->tel(),

                        Textarea::make('address')->label('Alamat Lengkap')->rows(3),
                    ]),
                ])
                ->columnSpanFull(),

            // =========================
            // FOTO PROFIL
            // =========================
            Section::make('Foto Profil')
                ->schema([
                    FileUpload::make('photo')
                        ->label('Unggah Foto')
                        ->image()
                        ->imageEditor()
                        ->directory('user-photos')
                        ->disk('public'),
                ])
                ->columnSpanFull(),

            // =========================
            // GANTI PASSWORD
            // =========================
            Section::make('Ganti Password')
                ->schema([
                    TextInput::make('current_password')
                        ->label('Password Saat Ini')
                        ->password()
                        ->revealable()
                        ->requiredWith('new_password')
                        ->rule('current_password'),

                    TextInput::make('new_password')
                        ->label('Password Baru')
                        ->password()
                        ->revealable()
                        ->rule(Password::default())
                        ->confirmed()
                        ->dehydrated(fn ($state) => filled($state))
                        ->afterStateUpdated(fn ($state, $set) => filled($state) ? $set('password', Hash::make($state)) : null),

                    TextInput::make('new_password_confirmation')
                        ->label('Konfirmasi Password Baru')
                        ->password()
                        ->revealable()
                        ->requiredWith('new_password'),
                ])
                ->columnSpanFull(),

        ]);
    }
}