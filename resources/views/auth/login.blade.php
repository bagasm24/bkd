<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Welcome back!') }}</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif   
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <!-- Input NIDN -->
            <div>
                <x-label for="nidn" value="{{ __('NIDN / Username') }}" />
                <x-input id="nidn" type="text" name="nidn" :value="old('nidn')" required autofocus />
            </div>

            <!-- Input Password -->
            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <div class="relative">
                    <x-input id="password" type="password" name="password" required autocomplete="current-password" class="pr-10" />
                    <button type="button" id="togglePassword" class="absolute right-2 top-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                {{-- <div class="mr-1">
                    <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                </div> --}}
            @endif
            <x-button class="">
                {{ __('Sign in') }}
            </x-button>
        </div>
    </form>
    <x-validation-errors class="mt-4" />   
    <!-- Footer -->
    {{-- <div class="pt-5 mt-6 border-t border-gray-100 dark:border-gray-700/60">
        <div class="text-sm">
            {{ __('Don\'t you have an account?') }} <a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="{{ route('register') }}">{{ __('Sign Up') }}</a>
        </div>
    </div> --}}
</x-authentication-layout>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Optional: Change icon if needed
        this.querySelector('svg').classList.toggle('text-gray-500');
        this.querySelector('svg').classList.toggle('text-blue-500');
    });
</script>