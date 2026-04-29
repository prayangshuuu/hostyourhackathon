@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">System Settings</h1>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 24px;">

            {{-- Card 1: General --}}
            <x-card title="General">
                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="app_name">Site Name</label>
                    <input type="text" name="app_name" id="app_name" class="form-input" value="{{ old('app_name', $settings->get('app_name', config('app.name'))) }}">
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="app_url">Site URL</label>
                    <input type="url" name="app_url" id="app_url" class="form-input" value="{{ old('app_url', $settings->get('app_url', config('app.url'))) }}">
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="support_email">Support Email</label>
                    <input type="email" name="support_email" id="support_email" class="form-input" value="{{ old('support_email', $settings->get('support_email')) }}">
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="form-label">Logo Upload</label>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        @if($settings->get('app_logo'))
                            <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--surface-alt); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img src="{{ Storage::url($settings->get('app_logo')) }}" alt="Logo" style="max-width: 100%; max-height: 100%;">
                            </div>
                        @else
                            <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--surface-alt); border: 1px dashed var(--border-subtle); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 12px;">
                                No Logo
                            </div>
                        @endif
                        <input type="file" name="app_logo" id="app_logo" class="form-input" accept="image/*" style="flex: 1;">
                    </div>
                </div>
            </x-card>

            {{-- Card 2: Mail / SMTP --}}
            <x-card title="Mail Settings">
                <div class="form-grid-2">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" for="smtp_host">SMTP Host</label>
                        <input type="text" name="smtp_host" id="smtp_host" class="form-input" value="{{ old('smtp_host', $settings->get('smtp_host')) }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" for="smtp_port">SMTP Port</label>
                        <input type="number" name="smtp_port" id="smtp_port" class="form-input" value="{{ old('smtp_port', $settings->get('smtp_port')) }}">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" for="smtp_username">SMTP Username</label>
                        <input type="text" name="smtp_username" id="smtp_username" class="form-input" value="{{ old('smtp_username', $settings->get('smtp_username')) }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" for="smtp_password">SMTP Password</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="password" name="smtp_password" id="smtp_password" class="form-input" placeholder="••••••••" style="flex: 1;">
                            <button type="button" class="btn btn-ghost" onclick="const p = document.getElementById('smtp_password'); p.type = p.type === 'password' ? 'text' : 'password';">Show</button>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="smtp_encryption">SMTP Encryption</label>
                    <select name="smtp_encryption" id="smtp_encryption" class="form-input">
                        <option value="tls" {{ old('smtp_encryption', $settings->get('smtp_encryption')) === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('smtp_encryption', $settings->get('smtp_encryption')) === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ old('smtp_encryption', $settings->get('smtp_encryption')) === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
                <div class="form-grid-2">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" for="mail_from_name">Mail From Name</label>
                        <input type="text" name="mail_from_name" id="mail_from_name" class="form-input" value="{{ old('mail_from_name', $settings->get('mail_from_name')) }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" for="mail_from_address">Mail From Address</label>
                        <input type="email" name="mail_from_address" id="mail_from_address" class="form-input" value="{{ old('mail_from_address', $settings->get('mail_from_address')) }}">
                    </div>
                </div>

                <div style="margin-top: 12px; border-top: 1px solid var(--border-subtle); padding-top: 16px;">
                    <button type="button" class="btn btn-secondary" onclick="fetch('{{ route('admin.settings.test-email') }}', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(r=>r.json()).then(d=>alert(d.message)).catch(e=>alert('Error sending test email'))">
                        Send Test Email
                    </button>
                </div>
            </x-card>

            {{-- Card 3: Features --}}
            <x-card title="Feature Flags">
                <style>
                    .toggle-checkbox { display: none; }
                    .toggle-label { display: flex; justify-content: space-between; align-items: center; cursor: pointer; padding: 16px 0; border-bottom: 1px solid var(--border-subtle); }
                    .toggle-label:last-child { border-bottom: none; }
                    .toggle-track { width: 40px; height: 22px; border-radius: 99px; background: var(--border); position: relative; transition: background 150ms ease; }
                    .toggle-thumb { width: 18px; height: 18px; border-radius: 99px; background: white; position: absolute; top: 2px; left: 0; transform: translateX(2px); transition: transform 150ms ease; }
                    .toggle-checkbox:checked + .toggle-track { background: var(--accent); }
                    .toggle-checkbox:checked + .toggle-track .toggle-thumb { transform: translateX(20px); }
                </style>
                @php
                    $features = [
                        'allow_registration' => ['User Registration', 'Allow new users to register accounts'],
                        'allow_multiple_hackathons' => ['Multiple Active Hackathons', 'Allow organizers to run more than one active hackathon at a time'],
                        'enable_google_oauth' => ['Google OAuth', 'Show Google sign-in button on auth pages'],
                        'enable_submissions' => ['Idea Submissions', 'Allow teams to submit project ideas'],
                        'enable_judging' => ['Judging', 'Allow judges to score submissions'],
                        'enable_leaderboard' => ['Public Leaderboard', 'Allow leaderboard to be made public by organizers'],
                    ];
                @endphp
                @foreach($features as $key => [$label, $desc])
                    <label class="toggle-label">
                        <div>
                            <div style="font-size: 14px; font-weight: 500; color: var(--text-primary);">{{ $label }}</div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ $desc }}</div>
                        </div>
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" class="toggle-checkbox" {{ $settings->get($key, true) ? 'checked' : '' }}>
                        <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    </label>
                @endforeach
            </x-card>

            {{-- Card 4: Danger Zone --}}
            <x-card title="Danger Zone" style="border: 1px solid rgba(220,38,38,0.15);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 14px; font-weight: 500; color: var(--text-primary);">Clear Cache</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Flushes all application cache, including settings.</div>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="fetch('{{ route('admin.settings.clear-cache') }}', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(()=>window.location.reload())">Clear Cache</button>
                </div>
            </x-card>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Save Settings</button>
        </div>
    </form>
@endsection
