<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;

class UserResource extends Resource
{
    public static $model = \App\Models\User::class;
    
    public static function routes()
    {
        // Return empty routes - we're using custom admin panel
        return [];
    }
    
    public static function form($form)
    {
        return $form;
    }
    
    public static function table($table)
    {
        return $table;
    }
    
    public static function relations()
    {
        return [];
    }
}

