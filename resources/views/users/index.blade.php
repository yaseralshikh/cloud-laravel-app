<x-layouts::app :title="__('Users')">
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Browse registered users.') }}</flux:subheading>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Joined') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($users as $user)
                            <tr class="text-neutral-900 dark:text-neutral-100">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300">{{ $user->created_at?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-neutral-600 dark:text-neutral-300">
                                    {{ __('No users found.') }}
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
</x-layouts::app>
