<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-0">
        @csrf
        @method('patch')

        <x-input label="Full Name" name="name" :value="old('name', $user->name)" :error="$errors->first('name')" required autofocus />
        <x-input label="Email Address" name="email" type="email" :value="old('email', $user->email)" :error="$errors->first('email')" required />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mb-5 p-3 bg-amber-50 border border-amber-100 rounded-lg">
                <p class="text-xs text-amber-800">
                    Your email address is unverified.
                    <button form="send-verification" class="font-bold underline hover:no-underline">Click here to re-send verification.</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="text-xs text-green-600 font-bold mt-2">A new verification link has been sent.</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4 pt-2">
            <x-button type="submit" variant="primary">Save Changes</x-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs text-slate-500 font-medium">Saved.</p>
            @endif
        </div>
    </form>
    
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
</section>
