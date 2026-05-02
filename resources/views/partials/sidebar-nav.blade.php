@php
$role = auth()->user()->roles->first()?->name;
$isSingle = $isSingleMode;

$navItem = function(string $route, string $label, string $icon, string $routePattern = null) {
  $active = request()->routeIs($routePattern ?? $route);
  return compact('route','label','icon','active');
};

$items = [];
if ($role === 'super_admin' || $role === 'organizer') {
  $items = [
    ['label'=>'MANAGE', 'children' => [
      $navItem('dashboard', 'Dashboard', 'home'),
      $isSingle
        ? $navItem('organizer.hackathons.show', 'Segments', 'puzzle-piece', 'organizer.segments*')
        : $navItem('organizer.hackathons.index', 'My Hackathons', 'trophy', 'organizer.hackathons*'),
      $navItem('organizer.teams.index', 'Teams', 'users', 'organizer.teams*'),
      $navItem('organizer.submissions.index', 'Submissions', 'document-text', 'organizer.submissions*'),
      $navItem('organizer.judges.index', 'Judges', 'star', 'organizer.judges*'),
      $navItem('organizer.announcements.index', 'Announcements', 'megaphone', 'organizer.announcements*'),
    ]],
  ];
  if ($role === 'super_admin') {
    $items[] = ['label'=>'ADMIN', 'children' => [
      $navItem('admin.users.index', 'Users', 'users', 'admin.users*'),
      $navItem('admin.settings', 'Settings', 'cog-6-tooth'),
    ]];
  }
} elseif ($role === 'judge') {
  $items = [['label'=>'JUDGING', 'children' => [
    $navItem('dashboard', 'Dashboard', 'home'),
    $navItem('judge.dashboard', 'My Submissions', 'document-text', 'judge.*'),
  ]]];
} else {
  $items = [['label'=>'MENU', 'children' => [
    $navItem('dashboard', 'Dashboard', 'home'),
    $isSingle
      ? $navItem('single.segments.index', 'Browse Segments', 'puzzle-piece', 'single.segments*')
      : $navItem('hackathons.index', 'Browse Hackathons', 'trophy', 'hackathons*'),
    $isSingle
      ? $navItem('single.teams.my', 'My Team', 'users', 'single.teams*')
      : $navItem('teams.index', 'My Team', 'users', 'teams*'),
    $isSingle
      ? $navItem('single.submissions.my', 'My Submission', 'document-text', 'single.submissions*')
      : $navItem('submissions.index', 'My Submission', 'document-text', 'submissions*'),
    $navItem('announcements.index', 'Announcements', 'megaphone', 'announcements*'),
    $isSingle
      ? $navItem('single.results', 'Results', 'trophy', 'single.results')
      : $navItem('leaderboard.show', 'Leaderboard', 'trophy', 'leaderboard*'),
  ]]];
}
@endphp

@foreach($items as $section)
  <div class="mb-1">
    <p class="px-2.5 py-1 text-2xs font-semibold text-slate-400 uppercase tracking-[0.07em]">{{ $section['label'] }}</p>
    @foreach($section['children'] as $item)
      <a href="{{ route($item['route']) }}" class="flex items-center gap-2.5 h-[34px] px-2.5 rounded-lg text-xs font-medium transition-colors {{ $item['active'] ? 'bg-accent-50 text-accent-600 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-4 h-4 flex-shrink-0" />
        {{ $item['label'] }}
      </a>
    @endforeach
  </div>
@endforeach
