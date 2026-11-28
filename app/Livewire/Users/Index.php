<?php

namespace App\Livewire\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Role;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search', history: true)]
    public string $search = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?int $editingUserId = null;

    public string $role = '';

    #[Computed]
    public function roles(): array
    {
        return Role::orderBy('id')->get()
            ->mapWithKeys(fn (Role $role) => [$role->name => $role->translatedDisplayName()])
            ->all();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
        $this->resetValidation();
        $this->role = 'user';
    }

    public function createUser(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles([$this->role]);

        $this->resetForm();
        $this->showCreateModal = false;
        session()->flash('status', __('messages.user-created'));
    }

    public function editUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showEditModal = true;
        $this->role = $user->roles->first()?->name ?? '';
        $this->resetValidation();
    }

    public function updateUser(): void
    {
        $user = User::findOrFail($this->editingUserId);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($validated['password'] !== null && $validated['password'] !== '') {
            $user->password = Hash::make($validated['password']);
        }

        // يمنع المستخدم من تغيير دوره بنفسه حتى لا يفقد صلاحية الدخول
        if (! $user->is(auth()->user())) {
            $user->syncRoles([$this->role]);
        }

        $user->save();

        $this->resetForm();
        $this->showEditModal = false;
        session()->flash('status', __('messages.user-updated'));
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        Flux::modal('delete-user')->close();

        if ($user->is(auth()->user())) {
            session()->flash('error', __('messages.cannot-delete-own-account'));

            return;
        }

        $user->delete();
        session()->flash('status', __('messages.user-deleted'));
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'editingUserId']);
        $this->resetValidation();
    }

    public function render()
    {
        $search = trim($this->search);

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.users.index', compact('users'))
            ->layout('layouts.app', ['title' => __('messages.users')]);
    }
}
