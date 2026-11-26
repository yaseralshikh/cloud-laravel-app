<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('verified users can see the users page and search', function () {
    $match = User::factory()->create(['name' => 'Searchable Person']);
    User::factory()->create(['name' => 'Unrelated Person']);

    $this->get(route('users.index', ['search' => 'Searchable']))
        ->assertOk()
        ->assertSee('Searchable Person')
        ->assertDontSee('Unrelated Person');
});

test('users can be created', function () {
    $this->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'new-user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new-user@example.com',
    ]);
});

test('users can be updated', function () {
    $target = User::factory()->create();

    $this->put(route('users.update', $target), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => '',
        'password_confirmation' => '',
    ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $target->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('users can be deleted but cannot delete themselves', function () {
    $target = User::factory()->create();

    $this->delete(route('users.destroy', $target))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', ['id' => $target->id]);

    $this->delete(route('users.destroy', $this->user))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', ['id' => $this->user->id]);
});
