<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

test('site and role management render in the selected language', function (string $locale, string $direction) {
    $this->withSession(['locale' => $locale])
        ->get(route('login'))
        ->assertOk()
        ->assertSeeHtml('dir="'.$direction.'"')
        ->assertSee(__('messages.log-in-to-account', [], $locale));

    $admin = User::factory()->create();
    $role = Role::create(['name' => 'superadmin', 'display_name' => 'Superadmin']);
    $permission = Permission::create(['name' => 'users-read', 'display_name' => 'Read Users']);
    $role->permissions()->attach($permission);
    $admin->addRole($role);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee(__('messages.add-user', [], $locale));

    $this->get(route('laratrust.roles.create'))
        ->assertOk()
        ->assertSeeHtml('dir="'.$direction.'"')
        ->assertSee(__('messages.new-role', [], $locale))
        ->assertSee(__('messages.permission-users-read', [], $locale));

    $this->get(route('laratrust.permissions.index'))
        ->assertOk()
        ->assertSee(__('messages.permission-users-read', [], $locale));
})->with([
    'Arabic' => ['ar', 'rtl'],
    'English' => ['en', 'ltr'],
]);
