<section class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 leading-relaxed max-w-md">
            Once your account is deleted, all of its resources and data will be permanently deleted. Please download any data you wish to retain.
        </p>
        <x-button variant="danger" x-data="" x-on:click.prevent="document.getElementById('confirm-user-deletion').classList.remove('hidden'); document.getElementById('confirm-user-deletion').classList.add('flex')">
            Delete Account
        </x-button>
    </div>

    <x-modal id="confirm-user-deletion" title="Are you sure you want to delete your account?">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <p class="text-sm text-slate-600 mb-6">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <x-input label="Password" name="password" type="password" placeholder="Confirm your password" :error="$errors->userDeletion->first('password')" required />

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                <x-button variant="secondary" onclick="document.getElementById('confirm-user-deletion').classList.add('hidden'); document.getElementById('confirm-user-deletion').classList.remove('flex')">
                    Cancel
                </x-button>
                <x-button type="submit" variant="danger">
                    Confirm Deletion
                </x-button>
            </div>
        </form>
    </x-modal>
</section>
