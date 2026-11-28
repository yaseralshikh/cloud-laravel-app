<x-layouts::auth :title="__('messages.registration')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('messages.register')" :description="__('messages.registration-description')" />

        <form method="POST" action="{{ route('register.store') }}" data-submit-loading class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="name"
                :label="__('messages.name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
            />

            <flux:input
                name="email"
                :label="__('messages.email-address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
            />

            <flux:input
                name="password"
                :label="__('messages.password')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('messages.confirm-password')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('messages.create-account') }}
            </flux:button>
        </form>

        <flux:text class="text-center">
            {{ __('messages.already-have-account') }}
            <flux:link :href="route('login')" wire:navigate>{{ __('messages.log-in') }}</flux:link>
        </flux:text>
    </div>
</x-layouts::auth>
