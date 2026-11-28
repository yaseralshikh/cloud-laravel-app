<x-layouts::app :title="__('messages.users')">
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('messages.users') }}</flux:heading>
                <flux:subheading>{{ __('messages.manage-users') }}</flux:subheading>
            </div>

            <flux:modal.trigger name="create-user">
                <flux:button icon="plus" variant="primary">{{ __('messages.add-user') }}</flux:button>
            </flux:modal.trigger>
        </div>

        @if (session('status'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 4000)"
                x-show="show"
                x-transition.opacity.duration.500ms
            >
                <flux:callout variant="success" icon="check-circle">
                    {{ session('status') }}
                </flux:callout>
            </div>
        @endif

        @if (session('error'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 6000)"
                x-show="show"
                x-transition.opacity.duration.500ms
            >
                <flux:callout variant="danger" icon="exclamation-circle" dismissible>
                    {{ session('error') }}
                </flux:callout>
            </div>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-3">
            <form method="GET" action="{{ route('users.index') }}" class="flex w-full max-w-md items-center gap-2">
                <flux:input
                    name="search"
                    icon="magnifying-glass"
                    value="{{ $search }}"
                    placeholder="{{ __('messages.search-by-name-or-email') }}"
                />
                <flux:button type="submit" variant="primary">{{ __('messages.search') }}</flux:button>
                @if ($search !== '')
                    <flux:button :href="route('users.index')" variant="ghost">{{ __('messages.clear') }}</flux:button>
                @endif
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('messages.name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('messages.email') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('messages.joined') }}</th>
                            <th scope="col" class="px-6 py-3 text-right font-medium">{{ __('messages.actions') }}</th>
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
                                            <flux:button size="sm" icon="pencil" variant="ghost">{{ __('messages.edit') }}</flux:button>
                                        </flux:modal.trigger>

                                        @unless ($user->is(auth()->user()))
                                            <flux:modal.trigger name="delete-user-{{ $user->id }}">
                                                <flux:button size="sm" icon="trash" variant="danger">{{ __('messages.delete') }}</flux:button>
                                            </flux:modal.trigger>
                                        @endunless
                                    </div>

                                    <flux:modal name="edit-user-{{ $user->id }}" class="max-w-lg">
                                        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <flux:heading size="lg">{{ __('messages.edit-user') }}</flux:heading>
                                                <flux:subheading>{{ __('messages.edit-user-description') }}</flux:subheading>
                                            </div>

                                            <div class="space-y-4">
                                                <flux:input name="name" label="{{ __('messages.name') }}" value="{{ old('name') ?: $user->name }}" required />
                                                <flux:input name="email" type="email" label="{{ __('messages.email') }}" value="{{ old('email') ?: $user->email }}" required />
                                                <flux:input name="password" type="password" label="{{ __('messages.new-password') }}" placeholder="{{ __('messages.keep-current-password') }}" />
                                                <flux:input name="password_confirmation" type="password" label="{{ __('messages.confirm-new-password') }}" />
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
                                                    <flux:button variant="ghost">{{ __('messages.cancel') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="primary">{{ __('messages.save-changes') }}</flux:button>
                                            </div>
                                        </form>
                                    </flux:modal>

                                    @unless ($user->is(auth()->user()))
                                        <flux:modal name="delete-user-{{ $user->id }}" class="max-w-md">
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="space-y-6">
                                                @csrf
                                                @method('DELETE')
                                                <div>
                                                    <flux:heading size="lg">{{ __('messages.delete-user') }}</flux:heading>
                                                    <flux:subheading>{{ __('messages.confirm-delete-user', ['name' => $user->name]) }}</flux:subheading>
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">{{ __('messages.cancel') }}</flux:button>
                                                    </flux:modal.close>
                                                    <flux:button type="submit" variant="danger">{{ __('messages.delete') }}</flux:button>
                                                </div>
                                            </form>
                                        </flux:modal>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-neutral-600 dark:text-neutral-300">
                                    {{ $search !== '' ? __('messages.no-matching-users') : __('messages.no-users') }}
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
                <flux:heading size="lg">{{ __('messages.add-user') }}</flux:heading>
                <flux:subheading>{{ __('messages.create-user-description') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input name="name" label="{{ __('messages.name') }}" value="{{ old('name') }}" required />
                <flux:input name="email" type="email" label="{{ __('messages.email') }}" value="{{ old('email') }}" required />
                <flux:input name="password" type="password" label="{{ __('messages.password') }}" required />
                <flux:input name="password_confirmation" type="password" label="{{ __('messages.confirm-password') }}" required />
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
                    <flux:button variant="ghost">{{ __('messages.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('messages.create-user') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</x-layouts::app>
