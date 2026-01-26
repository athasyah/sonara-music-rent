<?php 

namespace App\Services;

class ActivityLogService
{
    public function logActivity(string $action, string $module, string $description)
    {
        return [
            'user_id' => auth()->user()->id,
            'action' => $action,
            'module' => $module,
            'description' => $description,
        ];
    }
}