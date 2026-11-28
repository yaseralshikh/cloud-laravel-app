@extends('laratrust::panel.layout')

@section('title', __('messages.roles'))

@section('actions')
    <x-laratrust::button :href="route('laratrust.roles.create')">{{ __('messages.new-role') }}</x-laratrust::button>
@endsection

@section('content')
    <div x-data="laratrustConfirm">
        <x-laratrust::card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="lt-th">{{ __('messages.display-name') }}</th>
                            <th class="lt-th hidden sm:table-cell">{{ __('messages.name') }}</th>
                            <th class="lt-th">{{ __('messages.permissions') }}</th>
                            <th class="lt-th"><span class="sr-only">{{ __('messages.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="lt-td font-medium text-gray-900 dark:text-white">{{ $role->translatedDisplayName() }}</td>
                                <td class="lt-td hidden font-mono text-xs sm:table-cell">{{ $role->name }}</td>
                                <td class="lt-td">
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-white/10 dark:text-gray-300">{{ $role->permissions_count }}</span>
                                </td>
                                <td class="lt-td">
                                    <div class="flex items-center justify-end gap-4">
                                        @if ($role->isEditable)
                                            <a href="{{ route('laratrust.roles.edit', $role->getKey()) }}" class="lt-link">{{ __('messages.edit') }}</a>
                                        @else
                                            <a href="{{ route('laratrust.roles.show', $role->getKey()) }}" class="lt-link">{{ __('messages.details') }}</a>
                                        @endif

                                        @if ($role->isDeletable)
                                            <button
                                                type="button"
                                                class="font-medium text-red-600 hover:text-red-500 dark:text-red-400"
                                                x-on:click="open(@js(route('laratrust.roles.destroy', $role->getKey())), @js($role->translatedDisplayName()))"
                                            >{{ __('messages.delete') }}</button>
                                        @else
                                            <span class="cursor-not-allowed font-medium text-gray-400 dark:text-gray-600" title="{{ __('messages.cannot-delete-role') }}">{{ __('messages.delete') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="lt-td py-10 text-center text-gray-500">{{ __('messages.no-roles') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-laratrust::pagination :paginator="$roles" />
        </x-laratrust::card>

        <dialog x-ref="dialog" class="m-auto w-full max-w-md rounded-xl bg-white p-6 text-gray-900 shadow-xl backdrop:bg-gray-950/50 dark:bg-gray-900 dark:text-gray-100">
            <form method="POST" :action="action">
                @csrf
                @method('DELETE')
                <h2 class="text-lg font-semibold">{{ __('messages.delete-role') }}</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <span x-text="@js(__('messages.confirm-delete-role')).replace(':name', () => label)"></span>
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <x-laratrust::button variant="secondary" x-on:click="close">{{ __('messages.cancel') }}</x-laratrust::button>
                    <x-laratrust::button variant="danger" type="submit">{{ __('messages.delete') }}</x-laratrust::button>
                </div>
            </form>
        </dialog>
    </div>
@endsection
