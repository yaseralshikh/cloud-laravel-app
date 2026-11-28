<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('messages.appearance-settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('messages.appearance-settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('messages.appearance')" :subheading="__('messages.appearance-description')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('messages.light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('messages.dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('messages.system') }}</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>
