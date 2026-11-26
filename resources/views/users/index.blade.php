<x-layouts::app :title="__('Users')">
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Users') }}</flux:heading>
                <flux:subheading>{{ __('Manage registered users.') }}</flux:subheading>
            </div>

            <flux:modal.trigger name="create-user">
                <flux:button icon="plus" variant="primary">{{ __('Add User') }}</flux:button>
            </flux:modal.trigger>
        </div>

        @if (session('status'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('status') }}
            </flux:callout>
        @endif

        @if (session('error'))
            <flux:callout variant="danger" icon="exclamation-circle">
                {{ session('error') }}
            </flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-3">
            <form method="GET" action="{{ route('users.index') }}" class="flex w-full max-w-md items-center gap-2">
                <flux:input
                    name="search"
                    icon="magnifying-glass"
                    value="{{ $search }}"
                    placeholder="{{ __('Search by name or email') }}"
                />
                <flux:button type="submit" variant="primary">{{ __('Search') }}</flux:button>
                @if ($search !== '')
                    <flux:button :href="route('users.index')" variant="ghost">{{ __('Clear') }}</flux:button>
                @endif
            </form>
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
                            <tr class="text-neutral-900 dark:text-neutral-100">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300">{{ $user->created_at?->format('M j, Y') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <flux:modal.trigger name="edit-user-{{ $user->id }}">
                                            <flux:button size="sm" icon="pencil" variant="ghost">{{ __('Edit') }}</flux:button>
                                        </flux:modal.trigger>

                                        @unless ($user->is(auth()->user()))
                                            <flux:modal.trigger name="delete-user-{{ $user->id }}">
                                                <flux:button size="sm" icon="trash-2" variant="danger">{{ __('Delete') }}</flux:button>
                                            </flux:modal.trigger>
                                        @endunless
                                    </div>

                                    <flux:modal name="edit-user-{{ $user->id }}" class="max-w-lg">
                                        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <flux:heading size="lg">{{ __('Edit User') }}</flux:heading>
                                                <flux:subheading>{{ __('Update this user account.') }}</flux:subheading>
                                            </div>

                                            <div class="space-y-4">
                                                <flux:input name="name" label="{{ __('Name') }}" value="{{ old('name') ?: $user->name }}" required />
                                                <flux:input name="email" type="email" label="{{ __('Email') }}" value="{{ old('email') ?: $user->email }}" required />
                                                <flux:input name="password" type="password" label="{{ __('New Password') }}" placeholder="{{ __('Leave blank to keep current password') }}" />
                                                <flux:input name="password_confirmation" type="password" label="{{ __('Confirm New Password') }}" />
                                            </div>

                                            @if ($errors->any())
                                                <div class="rounded-lg border border-red-200 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:text-red-300">
                                                    <ul class="list-disc space-y-1 ps-5">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <div class="flex justify-end gap-2">
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="primary">{{ __('Save Changes') }}</flux:button>
                                            </div>
                                        </form>
                                    </flux:modal>

                                    @unless ($user->is(auth()->user()))
                                        <flux:modal name="delete-user-{{ $user->id }}" class="max-w-md">
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="space-y-6">
                                                @csrf
                                                @method('DELETE')
                                                <div>
                                                    <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                                                    <flux:subheading>{{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}</flux:subheading>
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                                    </flux:modal.close>
                                                    <flux:button type="submit" variant="danger">{{ __('Delete') }}</flux:button>
                                                </div>
                                            </form>
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
                <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <flux:modal name="create-user" class="max-w-lg">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf
            <div>
                <flux:heading size="lg">{{ __('Add User') }}</flux:heading>
                <flux:subheading>{{ __('Create a new user account.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input name="name" label="{{ __('Name') }}" value="{{ old('name') }}" required />
                <flux:input name="email" type="email" label="{{ __('Email') }}" value="{{ old('email') }}" required />
                <flux:input name="password" type="password" label="{{ __('Password') }}" required />
                <flux:input name="password_confirmation" type="password" label="{{ __('Confirm Password') }}" required />
            </div>

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:text-red-300">
                    <ul class="list-disc space-y-1 ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create User') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</x-layouts::app>
