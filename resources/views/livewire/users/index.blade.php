<div class="flex flex-col gap-6" x-data="{ deleteId: null, deleteName: '' }">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('messages.users') }}</flux:heading>
            <flux:subheading>{{ __('messages.manage-users') }}</flux:subheading>
        </div>

        <flux:button icon="plus" variant="primary" wire:click="openCreateModal">
            {{ __('messages.add-user') }}
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
                placeholder="{{ __('messages.search-by-name-or-email') }}"
                aria-label="{{ __('messages.search-by-name-or-email') }}"
                clearable />
        </div>

        <div class="flex min-h-5 flex-wrap items-center gap-x-3 text-sm">
            <flux:text>{{ trans_choice('messages.user-count', $users->total()) }}</flux:text>

            <div role="status">
                <span style="display: none" wire:loading.delay.short
                    wire:target="search, previousPage, nextPage, gotoPage, setPage">
                    <span class="inline-flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                        <flux:icon.loading class="size-4 motion-reduce:animate-none" aria-hidden="true" />
                        {{ __('messages.loading_users') }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
        <div class="overflow-x-auto motion-safe:transition-opacity"
            wire:loading.delay.short.class="opacity-60"
            wire:target="search, previousPage, nextPage, gotoPage, setPage">
            <table class="w-full text-start text-sm">
                <thead class="bg-neutral-50 text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-start font-medium sm:px-6">{{ __('messages.name') }}</th>
                        <th scope="col" class="hidden px-6 py-3 text-start font-medium md:table-cell">{{ __('messages.email') }}</th>
                        <th scope="col" class="hidden px-6 py-3 text-start font-medium md:table-cell">{{ __('messages.role') }}</th>
                        <th scope="col" class="hidden px-6 py-3 text-start font-medium lg:table-cell">{{ __('messages.joined') }}</th>
                        <th scope="col" class="px-4 py-3 text-end font-medium sm:px-6">
                            <span class="sr-only">{{ __('messages.actions') }}</span>
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
                                                <flux:badge size="sm" color="zinc">{{ __('messages.you') }}</flux:badge>
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
                                {{ $user->roles->first()?->translatedDisplayName() ?? __('messages.no-role') }}
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
                                    <flux:tooltip :content="__('messages.edit')">
                                        <flux:button size="sm" icon="pencil-square" variant="ghost"
                                            wire:click="editUser({{ $user->id }})"
                                            aria-label="{{ __('messages.edit-named-user', ['name' => $user->name]) }}" />
                                    </flux:tooltip>

                                    @if ($isSelf || $isSuperadmin)
                                        <flux:tooltip :content="$isSelf
                                            ? __('messages.cannot-delete-own-account')
                                            : __('messages.cannot-delete-superadmin')">
                                            <span>
                                                <flux:button size="sm" icon="trash" variant="ghost" disabled
                                                    aria-label="{{ __('messages.delete') }}" />
                                            </span>
                                        </flux:tooltip>
                                    @else
                                        <flux:tooltip :content="__('messages.delete')">
                                            <flux:button size="sm" icon="trash" variant="ghost"
                                                class="hover:text-red-600! dark:hover:text-red-400!"
                                                x-on:click="deleteId = {{ $user->id }}; deleteName = @js($user->name); $flux.modal('delete-user').show()"
                                                aria-label="{{ __('messages.delete-named-user', ['name' => $user->name]) }}" />
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
                                        {{ $search !== '' ? __('messages.no-matching-users') : __('messages.no-users') }}
                                    </flux:heading>
                                    @if ($search !== '')
                                        <flux:button size="sm" variant="ghost" wire:click="$set('search', '')">
                                            {{ __('messages.clear-search') }}
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" icon="plus" variant="primary" wire:click="openCreateModal">
                                            {{ __('messages.add-user') }}
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
                <flux:heading size="lg">{{ __('messages.add-user') }}</flux:heading>
                <flux:subheading>{{ __('messages.create-user-description') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('messages.name') }}" autocomplete="off" autofocus required />
                <flux:input wire:model="email" type="email" label="{{ __('messages.email') }}" autocomplete="off" required />

                <flux:select wire:model="role" label="{{ __('messages.role') }}" placeholder="{{ __('messages.select-role') }}">
                    @foreach ($this->roles as $roleName => $roleLabel)
                        <flux:select.option value="{{ $roleName }}">{{ $roleLabel }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="password" type="password" label="{{ __('messages.password') }}" autocomplete="new-password" viewable required />
                    <flux:input wire:model="password_confirmation" type="password" label="{{ __('messages.confirm-password') }}" autocomplete="new-password" viewable required />
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('messages.cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('messages.create-user') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit --}}
    <flux:modal name="edit-user" wire:model="showEditModal" class="w-full max-w-lg">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('messages.edit-user') }}</flux:heading>
                <flux:subheading>{{ __('messages.edit-user-description') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="name" label="{{ __('messages.name') }}" autocomplete="off" autofocus required />
                <flux:input wire:model="email" type="email" label="{{ __('messages.email') }}" autocomplete="off" required />

                @if ($editingUserId !== null && (string) $editingUserId !== (string) auth()->id())
                    <flux:select wire:model="role" label="{{ __('messages.role') }}" placeholder="{{ __('messages.select-role') }}">
                        @foreach ($this->roles as $roleName => $roleLabel)
                            <flux:select.option value="{{ $roleName }}">{{ $roleLabel }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="password" type="password" label="{{ __('messages.new-password') }}"
                        placeholder="{{ __('messages.keep-current-password') }}" autocomplete="new-password" viewable />
                    <flux:input wire:model="password_confirmation" type="password" label="{{ __('messages.confirm-new-password') }}"
                        autocomplete="new-password" viewable />
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('messages.cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('messages.save-changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete --}}
    <flux:modal name="delete-user" class="w-full max-w-md">
        <form wire:submit="deleteUser(deleteId)" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('messages.delete-user') }}</flux:heading>
                <flux:subheading
                    x-text="@js(__('messages.confirm-delete-user')).replace(':name', () => deleteName)"></flux:subheading>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button variant="ghost">{{ __('messages.cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="danger">
                    {{ __('messages.delete') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
