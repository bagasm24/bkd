<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Current Password') }}" />
            <div class="relative">
                <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model.live="state.current_password" autocomplete="current-password" />
                <button type="button" id="toggleCurrentPassword" class="absolute right-2 top-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('New Password') }}" />
            <div class="relative">
                <x-input id="password" type="password" class="mt-1 block w-full" wire:model.live="state.password" autocomplete="new-password" />
                <button type="button" id="togglePassword" class="absolute right-2 top-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <div class="relative">
                <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model.live="state.password_confirmation" autocomplete="new-password" />
                <button type="button" id="togglePasswordConfirmation" class="absolute right-2 top-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved. Password Berhasil Diubah') }}
        </x-action-message>

        <x-button>
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
<script>
    document.getElementById('toggleCurrentPassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('current_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Optional: Change icon if needed
        this.querySelector('svg').classList.toggle('text-gray-500');
        this.querySelector('svg').classList.toggle('text-blue-500');
    });
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Optional: Change icon if needed
        this.querySelector('svg').classList.toggle('text-gray-500');
        this.querySelector('svg').classList.toggle('text-blue-500');
    });
    document.getElementById('togglePasswordConfirmation').addEventListener('click', function () {
        const passwordInput = document.getElementById('password_confirmation');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Optional: Change icon if needed
        this.querySelector('svg').classList.toggle('text-gray-500');
        this.querySelector('svg').classList.toggle('text-blue-500');
    });
</script>
