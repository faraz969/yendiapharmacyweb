<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    public static $model = User::class;
    
    public static $icon = 'heroicon-o-users';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique('users', 'email', true)
                    ->maxLength(255),
                Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->only(Pages\CreateUser::class),
                Components\MultiSelect::make('roles')
                    ->options(Role::all()->pluck('name', 'id')),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')
                    ->searchable()
                    ->sortable(),
                Columns\Text::make('email')
                    ->searchable()
                    ->sortable(),
                Columns\Text::make('roles')
                    ->getValueUsing(function ($record) {
                        return $record->roles->pluck('name')->implode(', ');
                    })
                    ->label('Roles'),
                Columns\Text::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }

    public static function relations()
    {
        return [
            //
        ];
    }

    public static function routes()
    {
        return [
            Pages\ListUsers::routeTo('/', 'index'),
            Pages\CreateUser::routeTo('/create', 'create'),
            Pages\EditUser::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
