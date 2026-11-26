<?php

use App\Livewire\Users\Index;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('users page renders and supports search', function () {
    User::factory()->create(['name' => 'Searchable Person']);
    User::factory()->create(['name' => 'Unrelated Person']);

    Livewire::test(Index::class)
        ->set('search', 'Searchable')
        ->assertSee('Searchable Person')
        ->assertDontSee('Unrelated Person');
});

test('users can be created', function () {
    Livewire::test(Index::class)
        ->set('name', 'New User')
        ->set('email', 'new-user@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('createUser')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'new-user@example.com', 'name' => 'New User']);
});

test('users can be updated and deleted but not self-deleted', function () {
    $target = User::factory()->create();

    Livewire::test(Index::class)
        ->call('editUser', $target->id)
        ->set('name', 'Updated User')
        ->set('email', 'updated@example.com')
        ->call('updateUser')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Updated User']);

    Livewire::test(Index::class)->call('deleteUser', $target->id);
    $this->assertDatabaseMissing('users', ['id' => $target->id]);

    Livewire::test(Index::class)->call('deleteUser', $this->user->id);
    $this->assertDatabaseHas('users', ['id' => $this->user->id]);
});
