<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Table;

class CategoryResource extends Resource
{
    public static $model = Category::class;
    
    public static $icon = 'heroicon-o-folder';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Components\Textarea::make('description'),
                Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('categories'),
                Components\Toggle::make('is_active')
                    ->default(true),
                Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')
                    ->searchable()
                    ->sortable(),
                Columns\Image::make('image')
                    ->disk('public'),
                Columns\Boolean::make('is_active')
                    ->label('Active'),
                Columns\Text::make('sort_order')
                    ->sortable(),
                Columns\Text::make('products')
                    ->getValueUsing(function ($record) {
                        return $record->products()->count();
                    })
                    ->label('Products'),
                Columns\Text::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }

    public static function routes()
    {
        return [
            Pages\ListCategories::routeTo('/', 'index'),
            Pages\CreateCategory::routeTo('/create', 'create'),
            Pages\EditCategory::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
