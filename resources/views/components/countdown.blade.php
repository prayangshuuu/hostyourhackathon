@props(['deadline'])
<div x-data="countdown('{{ $deadline }}')" x-init="start()">
  <p class="countdown-display text-3xl font-bold text-accent-500 tabular-nums" x-text="display"></p>
  <p class="text-2xs text-slate-400 mt-1">until submission closes</p>
</div>
<script>
function countdown(deadline) {
  return {
    display: '--:--:--',
    interval: null,
    start() {
      this.tick();
      this.interval = setInterval(() => this.tick(), 1000);
    },
    tick() {
      const diff = new Date(deadline) - new Date();
      if (diff <= 0) {
        this.display = 'Closed';
        clearInterval(this.interval);
        return;
      }
      const h = Math.floor(diff / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      this.display = [h,m,s].map(n => String(n).padStart(2,'0')).join(':');
    }
  }
}
</script>
