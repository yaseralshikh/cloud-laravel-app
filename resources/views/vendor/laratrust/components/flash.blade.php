{{-- The panel's flash messages: laratrust-success, laratrust-warning and laratrust-error. --}}
@foreach (['success' => 'bg-green-50 text-green-800 ring-green-600/20 dark:bg-green-500/10 dark:text-green-300', 'warning' => 'bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300', 'error' => 'bg-red-50 text-red-800 ring-red-600/20 dark:bg-red-500/10 dark:text-red-300'] as $type => $classes)
    @if (session()->has("laratrust-{$type}"))
        @php
            $message = session("laratrust-{$type}");
            $messageKey = match ($message) {
                'Permission created successfully' => 'messages.permission-created',
                'Permission updated successfully' => 'messages.permission-updated',
                'Model was not specified in the request' => 'messages.missing-model',
                'Roles and permissions assigned successfully' => 'messages.roles-and-permissions-assigned',
                'Role created successfully' => 'messages.role-created',
                'The role is not editable' => 'messages.role-not-editable',
                'Role updated successfully' => 'messages.role-updated',
                'The role is not deletable' => 'messages.role-not-deletable',
                'Role is added to one or more users. It can not be deleted' => 'messages.role-assigned-to-users',
                'Role deleted successfully' => 'messages.role-deleted',
                default => null,
            };
        @endphp
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity
            role="{{ $type === 'success' ? 'status' : 'alert' }}"
            class="mb-6 flex items-start justify-between gap-4 rounded-lg px-4 py-3 text-sm ring-1 ring-inset {{ $classes }}"
        >
            <p>{{ $messageKey ? __($messageKey) : $message }}</p>
            <button type="button" x-on:click="show = false" class="opacity-70 hover:opacity-100" aria-label="{{ __('messages.dismiss') }}">&times;</button>
        </div>
    @endif
@endforeach
