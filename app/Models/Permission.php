<?php

namespace App\Models;

use Laratrust\Models\Permission as PermissionModel;

class Permission extends PermissionModel
{
    public $guarded = [];

    public function translatedDisplayName(): string
    {
        $key = 'messages.permission-'.$this->name;
        $label = __($key);

        return $label !== $key && (
            blank($this->display_name) || $this->display_name === __($key, [], 'en')
        ) ? $label : ($this->display_name ?: $this->name);
    }
}
