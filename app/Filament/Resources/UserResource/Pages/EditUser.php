<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    public static $resource = UserResource::class;
    
    protected $roles = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['roles'] = $this->record->roles->pluck('id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store roles before removing them
        $this->roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $roleIds = is_array($this->roles) ? $this->roles : ($this->roles ? [$this->roles] : []);
        $this->record->syncRoles(Role::whereIn('id', $roleIds)->pluck('name'));
    }
}
