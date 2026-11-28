<?php

namespace App\Models;

use Laratrust\Models\Role as RoleModel;

class Role extends RoleModel
{
    public $guarded = [];

    public function translatedDisplayName(): string
    {
        $key = 'messages.role-'.$this->name;
        $label = __($key);

        return $label !== $key && (
            blank($this->display_name) || $this->display_name === __($key, [], 'en')
        ) ? $label : ($this->display_name ?: $this->name);
    }
}
