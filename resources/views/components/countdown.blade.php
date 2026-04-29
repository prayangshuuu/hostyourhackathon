@props(['deadline'])

@php
    $deadlineIso = $deadline instanceof \Carbon\Carbon ? $deadline->toIso8601String() : $deadline;
@endphp

<div id="countdown-{{ uniqid() }}" data-deadline="{{ $deadlineIso }}" class="countdown-component">
    <div class="countdown-display" data-counter></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.countdown-component').forEach(function (el) {
        const deadline = new Date(el.dataset.deadline);
        const counterEl = el.querySelector('[data-counter]');

        function pad(n) { return String(n).padStart(2, '0'); }

        function update() {
            const now = new Date();
            const diff = deadline - now;

            if (diff <= 0) {
                counterEl.className = 'countdown-expired';
                counterEl.textContent = 'Submission Closed';
                return;
            }

            const hours = Math.floor(diff / 3600000);
            const mins = Math.floor((diff % 3600000) / 60000);
            const secs = Math.floor((diff % 60000) / 1000);

            counterEl.textContent = pad(hours) + ':' + pad(mins) + ':' + pad(secs);

            // Warning state when < 1 hour
            if (diff < 3600000) {
                counterEl.classList.add('countdown-warning');
            } else {
                counterEl.classList.remove('countdown-warning');
            }
        }

        update();
        setInterval(update, 1000);
    });
});
</script>
