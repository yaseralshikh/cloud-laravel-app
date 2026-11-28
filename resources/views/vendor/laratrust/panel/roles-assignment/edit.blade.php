@extends('laratrust::panel.layout')

@section('title', $user->name ?? $user->email ?? \Illuminate\Support\Str::headline(\Illuminate\Support\Str::singular($modelKey)).' #'.$user->getKey())

@section('content')
    <x-laratrust::card>
        @if ($teams !== null)
            {{-- Each team has its own assignments: the form shows and saves the ones of the chosen team. --}}
            <div class="flex flex-wrap items-center gap-3 border-b border-gray-200 px-4 py-3 dark:border-white/10">
                <label for="team" class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.team') }}</label>
                <select
                    id="team"
                    class="lt-input w-auto py-1.5 pr-8"
                    x-data
                    x-on:change="window.location = @js(route('laratrust.roles-assignment.edit', ['roles_assignment' => $user->getKey(), 'model' => $modelKey])) + '&team=' + encodeURIComponent($event.target.value)"
                >
                    <option value="">{{ __('messages.no-team') }}</option>
                    @foreach ($teams as $option)
                        <option value="{{ $option->getKey() }}" @selected((string) $team?->getKey() === (string) $option->getKey())>{{ $option->display_name ?? $option->name }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __($team === null ? 'messages.assignments-without-team' : 'messages.assignments-in-team') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('laratrust.roles-assignment.update', ['roles_assignment' => $user->getKey(), 'model' => $modelKey, 'team' => $team?->getKey()]) }}" class="space-y-8 p-4 sm:p-6">
            @csrf
            @method('PUT')

            @php
                $selectedRoles = collect(old('roles', $roles->filter->assigned->modelKeys()))->map(fn ($key) => (string) $key)->all();
            @endphp
            <x-laratrust::checklist
                name="roles"
                title="{{ __('messages.roles') }}"
                :options="$roles->map(fn ($role) => [
                    'value' => $role->getKey(),
                    'label' => $role->translatedDisplayName(),
                    'hint' => $role->name,
                    'checked' => in_array((string) $role->getKey(), $selectedRoles, true),
                    'locked' => $role->assigned && ! $role->isRemovable,
                ])->all()"
            />

            @if ($permissions)
                @php
                    $selectedPermissions = collect(old('permissions', $permissions->filter->assigned->modelKeys()))->map(fn ($key) => (string) $key)->all();
                @endphp
                <x-laratrust::checklist
                    name="permissions"
                    title="{{ __('messages.permissions') }}"
                    :options="$permissions->map(fn ($permission) => [
                        'value' => $permission->getKey(),
                        'label' => $permission->translatedDisplayName(),
                        'hint' => $permission->name,
                        'checked' => in_array((string) $permission->getKey(), $selectedPermissions, true),
                        'locked' => false,
                    ])->all()"
                />
            @endif

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6 dark:border-white/10">
                <x-laratrust::button variant="secondary" :href="route('laratrust.roles-assignment.index', ['model' => $modelKey])">{{ __('messages.cancel') }}</x-laratrust::button>
                <x-laratrust::button type="submit">{{ __('messages.save') }}</x-laratrust::button>
            </div>
        </form>
    </x-laratrust::card>
@endsection
