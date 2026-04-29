@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-page-header title="My Profile" />

    @if (session('success'))
        <div style="margin-bottom:24px;">
            <x-alert type="success" :message="session('success')" />
        </div>
    @endif
    @if (session('success_password'))
        <div style="margin-bottom:24px;">
            <x-alert type="success" :message="session('success_password')" />
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 7fr 5fr; gap: 24px;">
        {{-- Left Column --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            {{-- Card 1: Personal Information --}}
            <div class="card" style="padding: 24px;">
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 24px;">Personal Information</h2>
                
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                        <div style="position: relative;">
                            @if ($user->avatar)
                                <img id="avatar-preview" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div id="avatar-preview-fallback" style="width: 64px; height: 64px; border-radius: 50%; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 600;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <img id="avatar-preview" src="" alt="Avatar Preview" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; display: none;">
                            @endif
                        </div>
                        <div>
                            <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png,image/webp" style="display: none;" onchange="previewAvatar(event)">
                            <button type="button" onclick="document.getElementById('avatar-input').click()" style="background: none; border: none; color: var(--accent); font-size: 14px; font-weight: 500; cursor: pointer; padding: 0;">
                                Change photo
                            </button>
                            @error('avatar')
                                <p style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <x-input name="name" label="Name" :value="old('name', $user->name)" required />
                    </div>

                    <div style="margin-bottom: 24px;">
                        <x-input type="email" name="email" label="Email Address" :value="old('email', $user->email)" required />
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <x-button type="submit" variant="primary">Save Changes</x-button>
                    </div>
                </form>
            </div>

            {{-- Card 2: Change Password --}}
            <div class="card" style="padding: 24px;">
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 24px;">Change Password</h2>
                
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom: 16px;">
                        <x-input type="password" name="current_password" label="Current Password" required />
                    </div>

                    <div style="margin-bottom: 16px;">
                        <x-input type="password" name="password" id="new-password" label="New Password" required oninput="checkPasswordStrength()" />
                        <div style="height: 4px; background: var(--border-subtle); border-radius: 2px; margin-top: 8px; overflow: hidden;">
                            <div id="password-strength-bar" style="height: 100%; width: 0%; background: var(--danger); transition: all 0.3s ease;"></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <x-input type="password" name="password_confirmation" label="Confirm Password" required />
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <x-button type="submit" variant="primary">Update Password</x-button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column --}}
        <div>
            <div class="card" style="padding: 24px; text-align: center;">
                @if ($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: 0 auto 16px;">
                @else
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 600; margin: 0 auto 16px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">{{ $user->name }}</h3>
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 16px;">{{ $user->email }}</p>
                
                @php
                    $roleName = $user->roles->first()->name ?? 'participant';
                    $roleLabel = str_replace('_', ' ', $roleName);
                    
                    $roleVariant = match($roleName) {
                        'super_admin' => 'primary',
                        'organizer' => 'warning',
                        'judge' => 'success',
                        default => 'secondary'
                    };
                @endphp
                <div style="margin-bottom: 16px;">
                    <x-badge :variant="$roleVariant" style="text-transform: capitalize;">{{ $roleLabel }}</x-badge>
                </div>
                
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">Member since {{ $user->created_at->format('M j, Y') }}</p>
                
                <div style="display: flex; justify-content: space-around; border-top: 1px solid var(--border); padding-top: 16px;">
                    <div>
                        <div style="font-size: 22px; font-weight: 600; color: var(--accent);">{{ $user->teamMemberships()->count() }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Hackathons</div>
                    </div>
                    <div>
                        <div style="font-size: 22px; font-weight: 600; color: var(--accent);">{{ \App\Models\Submission::whereHas('team.members', function($q) use($user) { $q->where('user_id', $user->id); })->count() }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Submissions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    const fallback = document.getElementById('avatar-preview-fallback');
                    
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    if (fallback) {
                        fallback.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('new-password').value;
            const bar = document.getElementById('password-strength-bar');
            
            let strength = 0;
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]+/)) strength += 25;
            if (password.match(/[A-Z]+/)) strength += 25;
            if (password.match(/[0-9]+/)) strength += 25;
            
            bar.style.width = strength + '%';
            
            if (strength <= 25) {
                bar.style.background = 'var(--danger)';
            } else if (strength <= 50) {
                bar.style.background = 'var(--warning)';
            } else if (strength <= 75) {
                bar.style.background = '#eab308'; // yellow
            } else {
                bar.style.background = 'var(--success)';
            }
        }
    </script>
@endsection
