<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    public static $resource = UserResource::class;
    
    protected $roles = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store roles before removing them
        $this->roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->roles)) {
            $roleIds = is_array($this->roles) ? $this->roles : [$this->roles];
            $this->record->syncRoles(Role::whereIn('id', $roleIds)->pluck('name'));
        }
    }
}
