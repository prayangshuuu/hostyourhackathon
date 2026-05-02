<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-0">
        @csrf
        @method('put')

        <x-input label="Current Password" name="current_password" type="password" :error="$errors->updatePassword->first('current_password')" required autocomplete="current-password" />
        <x-input label="New Password" name="password" type="password" :error="$errors->updatePassword->first('password')" required autocomplete="new-password" />
        <x-input label="Confirm Password" name="password_confirmation" type="password" :error="$errors->updatePassword->first('password_confirmation')" required autocomplete="new-password" />

        <div class="flex items-center gap-4 pt-2">
            <x-button type="submit" variant="primary">Update Password</x-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs text-slate-500 font-medium">Saved.</p>
            @endif
        </div>
    </form>
</section>
