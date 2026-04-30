<footer class="bg-surface border-t border-border pt-10 pb-8">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-o-bolt class="w-6 h-6 text-accent" />
                    <span class="text-lg font-bold text-text-primary">{{ $appSettings->get('app_name', config('app.name')) }}</span>
                </div>
                <p class="text-sm text-text-secondary">Open-source hackathon management.</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-text-primary mb-4">Platform</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('hackathons.index') }}" class="text-sm text-text-secondary hover:text-accent transition-colors">Browse Hackathons</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm text-text-secondary hover:text-accent transition-colors">Sign In</a></li>
                    @if($appSettings->get('allow_registration', true))
                        <li><a href="{{ route('register') }}" class="text-sm text-text-secondary hover:text-accent transition-colors">Register</a></li>
                    @endif
                    <li><a href="/api/docs" class="text-sm text-text-secondary hover:text-accent transition-colors">API Docs</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-text-primary mb-4">Project</h3>
                <ul class="space-y-3">
                    <li><a href="https://github.com/prayangshuuu/hostyourhackathon" target="_blank" class="text-sm text-text-secondary hover:text-accent transition-colors">GitHub</a></li>
                    <li><a href="https://github.com/prayangshuuu/hostyourhackathon/issues" target="_blank" class="text-sm text-text-secondary hover:text-accent transition-colors">Report a Bug</a></li>
                    <li><a href="https://github.com/prayangshuuu/hostyourhackathon/blob/main/LICENSE" target="_blank" class="text-sm text-text-secondary hover:text-accent transition-colors">MIT License</a></li>
                    <li><span class="text-sm text-text-secondary">Support: {{ $appSettings->get('support_email', 'support@example.com') }}</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-border pt-6 mt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[13px] text-text-muted">&copy; {{ date('Y') }} {{ $appSettings->get('app_name', config('app.name')) }}. MIT License.</p>
            <p class="text-[13px] text-text-muted">Built with Laravel</p>
        </div>
    </div>
</footer>
