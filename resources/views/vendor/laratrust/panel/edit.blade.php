@extends('laratrust::panel.layout')

@section('title', $model ? __($type === 'role' ? 'messages.edit-role' : 'messages.edit-permission') : __($type === 'role' ? 'messages.new-role' : 'messages.new-permission'))

@section('content')
    @php
        $enterNameManually = $model === null && app()->isLocale('ar');
    @endphp

    <x-laratrust::card>
        <form
            method="POST"
            action="{{ $model ? route("laratrust.{$type}s.update", $model->getKey()) : route("laratrust.{$type}s.store") }}"
            x-data="laratrustNameFromDisplayName(@js(old('name', $model?->name ?? '')), @js(old('display_name', $model?->display_name ?? '')), @js($model === null && !$enterNameManually))"
            class="space-y-6 p-4 sm:p-6"
        >
            @csrf
            @if ($model)
                @method('PUT')
            @endif

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="display_name" class="lt-label">{{ __('messages.display-name') }}</label>
                    <input id="display_name" name="display_name" x-model="displayName" placeholder="{{ __('messages.permission-name-example') }}" autocomplete="off" class="lt-input mt-2">
                    @error('display_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="lt-label">{{ __('messages.name') }}</label>
                    <input id="name" name="name" x-model="name" @readonly(!$enterNameManually) @required($enterNameManually) dir="ltr" placeholder="edit-user-profile" autocomplete="off" aria-describedby="name-help" class="lt-input mt-2 font-mono text-gray-600 read-only:bg-gray-50 dark:text-gray-300 dark:read-only:bg-white/10">
                    <p id="name-help" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        @if ($model)
                            {{ __('messages.cannot-change-name') }}
                        @elseif ($enterNameManually)
                            {{ __('messages.manual-name-help') }}
                        @else
                            {{ __('messages.generated-name-help') }}
                        @endif
                    </p>
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="lt-label">{{ __('messages.description') }}</label>
                <textarea id="description" name="description" rows="3" placeholder="{{ __($type === 'role' ? 'messages.role-description-placeholder' : 'messages.permission-description-placeholder') }}" class="lt-input mt-2">{{ old('description', $model?->description) }}</textarea>
                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            @if ($type === 'role')
                @php
                    $selected = collect(old('permissions', $permissions->filter->assigned->modelKeys()))->map(fn ($key) => (string) $key)->all();
                @endphp
                <x-laratrust::checklist
                    name="permissions"
                    title="{{ __('messages.permissions') }}"
                    :options="$permissions->map(fn ($permission) => [
                        'value' => $permission->getKey(),
                        'label' => $permission->translatedDisplayName(),
                        'hint' => $permission->name,
                        'checked' => in_array((string) $permission->getKey(), $selected, true),
                        'locked' => false,
                    ])->all()"
                />
            @endif

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6 dark:border-white/10">
                <x-laratrust::button variant="secondary" :href="route('laratrust.'.$type.'s.index')">{{ __('messages.cancel') }}</x-laratrust::button>
                <x-laratrust::button type="submit">{{ __('messages.save') }}</x-laratrust::button>
            </div>
        </form>
    </x-laratrust::card>
@endsection
