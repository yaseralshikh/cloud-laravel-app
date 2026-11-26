<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Manage registered users.') }}</flux:subheading>
        </div>

        <flux:button icon="plus" variant="primary" wire:click="openCreateModal">
            {{ __('Add User') }}
        </flux:button>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">{{ session('status') }}</flux:callout>
    @endif
    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle">{{ session('error') }}</flux:callout>
    @endif

    <div class="flex w-full max-w-md items-center gap-2">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search by name or email') }}" />
        @if ($search !== '')
            <flux:button variant="ghost" wire:click="$set('search', '')">{{ __('Clear') }}</flux:button>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Joined') }}</th>
                        <th scope="col" class="px-6 py-3 text-right font-medium">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="text-neutral-900 dark:text-neutral-100">
                            <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $user->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300">{{ $user->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300">{{ $user->created_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" icon="pencil" variant="ghost" wire:click="editUser({{ $user->id }})">{{ __('Edit') }}</flux:button>
                                    @unless ($user->is(auth()->user()))
                                        <flux:modal.trigger name="delete-user-{{ $user->id }}">
                                            <flux:button size="sm" icon="trash-2" variant="danger">{{ __('Delete') }}</flux:button>
                                        </flux:modal.trigger>
                                    @endunless
                                </div>
                                @unless ($user->is(auth()->user()))
                                    <flux:modal name="delete-user-{{ $user->id }}" class="max-w-md">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                                                <flux:subheading>{{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}</flux:subheading>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                                                <flux:button variant="danger" wire:click="deleteUser({{ $user->id }})">{{ __('Delete') }}</flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-neutral-600 dark:text-neutral-300">
                                {{ $search !== '' ? __('No users match your search.') : __('No users found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">{{ $users->links() }}</div>
        @endif
    </div>

    <flux:modal name="create-user" wire:model="showCreateModal" class="max-w-lg">
        <form wire:submit="createUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add User') }}</flux:heading>
                <flux:subheading>{{ __('Create a new user account.') }}</flux:subheading>
            </div>
            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('Name') }}" required />
                <flux:input wire:model="email" type="email" label="{{ __('Email') }}" required />
                <flux:input wire:model="password" type="password" label="{{ __('Password') }}" required />
                <flux:input wire:model="password_confirmation" type="password" label="{{ __('Confirm Password') }}" required />
            </div>
            <flux:error name="name" />
            <flux:error name="email" />
            <flux:error name="password" />
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create User') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="edit-user" wire:model="showEditModal" class="max-w-lg">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit User') }}</flux:heading>
                <flux:subheading>{{ __('Update this user account.') }}</flux:subheading>
            </div>
            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('Name') }}" required />
                <flux:input wire:model="email" type="email" label="{{ __('Email') }}" required />
                <flux:input wire:model="password" type="password" label="{{ __('New Password') }}" placeholder="{{ __('Leave blank to keep current password') }}" />
                <flux:input wire:model="password_confirmation" type="password" label="{{ __('Confirm New Password') }}" />
            </div>
            <flux:error name="name" />
            <flux:error name="email" />
            <flux:error name="password" />
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
