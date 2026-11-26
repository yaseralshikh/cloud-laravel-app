<div class="flex flex-col gap-6" x-data="{ deleteId: null, deleteName: '' }">
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
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)"
             x-show="show" x-transition.opacity.duration.300ms role="status">
            <flux:callout variant="success" icon="check-circle">{{ session('status') }}</flux:callout>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)"
             x-show="show" x-transition.opacity.duration.300ms role="alert">
            <flux:callout variant="danger" icon="exclamation-circle">{{ session('error') }}</flux:callout>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="w-full max-w-md">
            <flux:input
                type="search"
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="{{ __('Search by name or email') }}"
                aria-label="{{ __('Search by name or email') }}"
                clearable />
        </div>

        <flux:text class="text-sm">
            {{ __(':count users', ['count' => $users->total()]) }}
        </flux:text>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
        <div class="overflow-x-auto transition-opacity" wire:loading.class="opacity-60" wire:target="search">
            <table class="w-full text-start text-sm">
                <thead class="bg-neutral-50 text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-start font-medium sm:px-6">{{ __('Name') }}</th>
                        <th scope="col" class="hidden px-6 py-3 text-start font-medium md:table-cell">{{ __('Email') }}</th>
                        <th scope="col" class="hidden px-6 py-3 text-start font-medium md:table-cell">{{ __('Role') }}</th>
                        <th scope="col" class="hidden px-6 py-3 text-start font-medium lg:table-cell">{{ __('Joined') }}</th>
                        <th scope="col" class="px-4 py-3 text-end font-medium sm:px-6">
                            <span class="sr-only">{{ __('Actions') }}</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($users as $user)
                        @php
                            $isSelf = $user->is(auth()->user());
                            $isSuperadmin = $user->hasRole('superadmin');
                        @endphp
                        <tr wire:key="user-{{ $user->id }}" class="text-neutral-900 hover:bg-neutral-50 dark:text-neutral-100 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" :name="$user->name" />
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="truncate font-medium">{{ $user->name }}</span>
                                            @if ($isSelf)
                                                <flux:badge size="sm" color="zinc">{{ __('You') }}</flux:badge>
                                            @endif
                                        </div>
                                        <div class="truncate text-neutral-600 md:hidden dark:text-neutral-300">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="hidden whitespace-nowrap px-6 py-3 text-neutral-600 md:table-cell dark:text-neutral-300">
                                {{ $user->email }}
                            </td>
                            
                            <td class="hidden whitespace-nowrap px-6 py-3 text-neutral-600 md:table-cell dark:text-neutral-300">
                                {{ $user->roles->first()?->display_name ?: $user->roles->first()?->name ?: __('No role') }}
                            </td>

                            <td class="hidden whitespace-nowrap px-6 py-3 text-neutral-600 lg:table-cell dark:text-neutral-300">
                                @if ($user->created_at)
                                    <time datetime="{{ $user->created_at->toIso8601String() }}" title="{{ $user->created_at->diffForHumans() }}">
                                        {{ $user->created_at->translatedFormat('j M Y') }}
                                    </time>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-4 py-3 sm:px-6">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:tooltip :content="__('Edit')">
                                        <flux:button size="sm" icon="pencil-square" variant="ghost"
                                            wire:click="editUser({{ $user->id }})"
                                            aria-label="{{ __('Edit :name', ['name' => $user->name]) }}" />
                                    </flux:tooltip>

                                    @if ($isSelf || $isSuperadmin)
                                        <flux:tooltip :content="$isSelf
                                            ? __('You cannot delete your own account.')
                                            : __('Superadmin accounts cannot be deleted.')">
                                            <span>
                                                <flux:button size="sm" icon="trash" variant="ghost" disabled
                                                    aria-label="{{ __('Delete') }}" />
                                            </span>
                                        </flux:tooltip>
                                    @else
                                        <flux:tooltip :content="__('Delete')">
                                            <flux:button size="sm" icon="trash" variant="ghost"
                                                class="hover:text-red-600! dark:hover:text-red-400!"
                                                x-on:click="deleteId = {{ $user->id }}; deleteName = @js($user->name); $flux.modal('delete-user').show()"
                                                aria-label="{{ __('Delete :name', ['name' => $user->name]) }}" />
                                        </flux:tooltip>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12">
                                <div class="flex flex-col items-center gap-3 text-center">
                                    <flux:icon name="{{ $search !== '' ? 'magnifying-glass' : 'users' }}" class="size-8 text-neutral-400" />
                                    <flux:heading>
                                        {{ $search !== '' ? __('No users match your search.') : __('No users found.') }}
                                    </flux:heading>
                                    @if ($search !== '')
                                        <flux:button size="sm" variant="ghost" wire:click="$set('search', '')">
                                            {{ __('Clear search') }}
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" icon="plus" variant="primary" wire:click="openCreateModal">
                                            {{ __('Add User') }}
                                        </flux:button>
                                    @endif
                                </div>
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

    {{-- Create --}}
    <flux:modal name="create-user" wire:model="showCreateModal" class="w-full max-w-lg">
        <form wire:submit="createUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add User') }}</flux:heading>
                <flux:subheading>{{ __('Create a new user account.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('Name') }}" autocomplete="off" autofocus required />
                <flux:input wire:model="email" type="email" label="{{ __('Email') }}" autocomplete="off" required />

                <flux:select wire:model="role" label="{{ __('Role') }}" placeholder="{{ __('Select role') }}">
                    @foreach ($this->roles as $roleName => $roleLabel)
                        <flux:select.option value="{{ $roleName }}">{{ $roleLabel }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="password" type="password" label="{{ __('Password') }}" autocomplete="new-password" viewable required />
                    <flux:input wire:model="password_confirmation" type="password" label="{{ __('Confirm Password') }}" autocomplete="new-password" viewable required />
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create User') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit --}}
    <flux:modal name="edit-user" wire:model="showEditModal" class="w-full max-w-lg">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit User') }}</flux:heading>
                <flux:subheading>{{ __('Update this user account.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('Name') }}" autocomplete="off" autofocus required />
                <flux:input wire:model="email" type="email" label="{{ __('Email') }}" autocomplete="off" required />

                <flux:select wire:model="role" label="{{ __('Role') }}" placeholder="{{ __('Select role') }}">
                    @foreach ($this->roles as $roleName => $roleLabel)
                        <flux:select.option value="{{ $roleName }}">{{ $roleLabel }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="password" type="password" label="{{ __('New Password') }}"
                        placeholder="{{ __('Leave blank to keep current password') }}" autocomplete="new-password" viewable />
                    <flux:input wire:model="password_confirmation" type="password" label="{{ __('Confirm New Password') }}"
                        autocomplete="new-password" viewable />
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Single delete confirmation (Alpine only, no component changes) --}}
    <flux:modal name="delete-user" class="w-full max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                <flux:subheading
                    x-text="@js(__('Are you sure you want to delete :name?')).replace(':name', deleteName)"></flux:subheading>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button variant="danger"
                    x-on:click="$wire.deleteUser(deleteId); $flux.modal('delete-user').close()">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>