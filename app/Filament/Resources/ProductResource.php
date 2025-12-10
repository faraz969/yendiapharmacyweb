<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Table;

class ProductResource extends Resource
{
    public static $model = Product::class;
    
    public static $icon = 'heroicon-o-cube';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->required(),
                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Components\Textarea::make('description'),
                Components\TextInput::make('sku')
                    ->required()
                    ->unique('products', 'sku')
                    ->maxLength(255),
                Components\TextInput::make('barcode')
                    ->maxLength(255),
                Components\FileUpload::make('images')
                    ->image()
                    ->disk('public')
                    ->directory('products'),
                Components\FileUpload::make('video')
                    ->disk('public')
                    ->directory('products/videos'),
                
                Components\Section::make('Pricing')
                    ->schema([
                        Components\TextInput::make('selling_price')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                        Components\TextInput::make('cost_price')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                    ]),
                
                Components\Section::make('Unit Conversion')
                    ->schema([
                        Components\Select::make('purchase_unit')
                            ->options([
                                'box' => 'Box',
                                'pack' => 'Pack',
                                'bottle' => 'Bottle',
                            ])
                            ->default('box')
                            ->required(),
                        Components\Select::make('selling_unit')
                            ->options([
                                'tablet' => 'Tablet',
                                'capsule' => 'Capsule',
                                'ml' => 'ML',
                                'piece' => 'Piece',
                            ])
                            ->default('tablet')
                            ->required(),
                        Components\TextInput::make('conversion_factor')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ]),
                
                Components\Section::make('Prescription')
                    ->schema([
                        Components\Toggle::make('requires_prescription')
                            ->default(false),
                        Components\Textarea::make('prescription_notes'),
                    ]),
                
                Components\Section::make('Inventory')
                    ->schema([
                        Components\TextInput::make('min_stock_level')
                            ->numeric()
                            ->default(0),
                        Components\TextInput::make('max_stock_level')
                            ->numeric(),
                        Components\Toggle::make('track_expiry')
                            ->default(true),
                        Components\Toggle::make('track_batch')
                            ->default(true),
                    ]),
                
                Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')
                    ->searchable()
                    ->sortable(),
                Columns\Text::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Columns\Text::make('sku')
                    ->searchable(),
                Columns\Text::make('selling_price')
                    ->getValueUsing(function ($record) {
                        return '$' . number_format($record->selling_price, 2);
                    })
                    ->sortable(),
                Columns\Boolean::make('requires_prescription')
                    ->label('Rx Required'),
                Columns\Boolean::make('is_active')
                    ->label('Active'),
                Columns\Boolean::make('is_expired')
                    ->label('Expired'),
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
            Pages\ListProducts::routeTo('/', 'index'),
            Pages\CreateProduct::routeTo('/create', 'create'),
            Pages\EditProduct::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
