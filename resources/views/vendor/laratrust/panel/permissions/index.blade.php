@extends('laratrust::panel.layout')

@section('title', __('messages.permissions'))

@section('actions')
    @if (config('laratrust.panel.create_permissions'))
        <x-laratrust::button :href="route('laratrust.permissions.create')">{{ __('messages.new-permission') }}</x-laratrust::button>
    @endif
@endsection

@section('content')
    <x-laratrust::card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="lt-th">{{ __('messages.display-name') }}</th>
                        <th class="lt-th hidden sm:table-cell">{{ __('messages.name') }}</th>
                        <th class="lt-th hidden lg:table-cell">{{ __('messages.description') }}</th>
                        <th class="lt-th"><span class="sr-only">{{ __('messages.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse ($permissions as $permission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="lt-td font-medium text-gray-900 dark:text-white">{{ $permission->translatedDisplayName() }}</td>
                            <td class="lt-td hidden font-mono text-xs sm:table-cell">{{ $permission->name }}</td>
                            <td class="lt-td hidden max-w-sm truncate lg:table-cell">{{ $permission->description }}</td>
                            <td class="lt-td text-right">
                                <a href="{{ route('laratrust.permissions.edit', $permission->getKey()) }}" class="lt-link">{{ __('messages.edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="lt-td py-10 text-center text-gray-500">{{ __('messages.no-permissions-yet') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-laratrust::pagination :paginator="$permissions" />
    </x-laratrust::card>
@endsection
