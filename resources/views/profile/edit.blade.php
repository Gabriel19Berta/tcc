<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>{{ __('Perfil') }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="p-2 sm:p-8 bg-white shadow sm:rounded-lg">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            @include('profile.partials.update-password-form')
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
