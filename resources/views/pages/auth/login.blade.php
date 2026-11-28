<x-layouts::auth :title="__('messages.log-in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('messages.log-in-to-account')" :description="__('messages.log-in-description')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />


        <form method="POST" action="{{ route('login.store') }}" data-submit-loading class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('messages.email-address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('messages.password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('messages.password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('messages.forgot-your-password') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('messages.remember-me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('messages.log-in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <flux:text class="text-center">
                {{ __('messages.no-account') }}
                <flux:link :href="route('register')" wire:navigate>{{ __('messages.sign-up') }}</flux:link>
            </flux:text>
        @endif
    </div>
</x-layouts::auth>
