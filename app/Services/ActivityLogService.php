<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log a user activity
     *
     * @param string $action
     * @param string|null $description
     * @param Model|null $model
     * @param array|null $properties
     * @param Request|null $request
     * @return ActivityLog
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?array $properties = null,
        ?Request $request = null
    ): ActivityLog {
        $request = $request ?? request();
        $user = Auth::user();

        $data = [
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description ?? self::generateDescription($action, $model),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }

        if ($properties) {
            $data['properties'] = $properties;
        }

        return ActivityLog::create($data);
    }

    /**
     * Generate a human-readable description from action and model
     */
    protected static function generateDescription(string $action, ?Model $model = null): string
    {
        $actionMap = [
            'login' => 'User logged in',
            'logout' => 'User logged out',
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'view' => 'Viewed',
        ];

        $baseDescription = $actionMap[$action] ?? ucfirst($action);

        if ($model) {
            $modelName = class_basename($model);
            return "{$baseDescription} {$modelName} #{$model->id}";
        }

        return $baseDescription;
    }

    /**
     * Log user login
     */
    public static function logLogin(?Request $request = null): ActivityLog
    {
        return self::log('login', 'User logged in', null, null, $request);
    }

    /**
     * Log user logout
     */
    public static function logLogout(?Request $request = null): ActivityLog
    {
        return self::log('logout', 'User logged out', null, null, $request);
    }

    /**
     * Log model creation
     */
    public static function logCreate(Model $model, ?array $properties = null, ?Request $request = null): ActivityLog
    {
        return self::log('create', null, $model, $properties, $request);
    }

    /**
     * Log model update
     */
    public static function logUpdate(Model $model, ?array $properties = null, ?Request $request = null): ActivityLog
    {
        return self::log('update', null, $model, $properties, $request);
    }

    /**
     * Log model deletion
     */
    public static function logDelete(Model $model, ?array $properties = null, ?Request $request = null): ActivityLog
    {
        return self::log('delete', null, $model, $properties, $request);
    }

    /**
     * Log custom action
     */
    public static function logAction(
        string $action,
        string $description,
        ?Model $model = null,
        ?array $properties = null,
        ?Request $request = null
    ): ActivityLog {
        return self::log($action, $description, $model, $properties, $request);
    }
}

