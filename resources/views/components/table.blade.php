@props(['headers' => [], 'emptyMessage' => 'No data available.'])

<div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; width: 100%; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: var(--surface-alt); border-bottom: 1px solid var(--border);">
            <tr>
                @foreach($headers as $header)
                    <th style="padding: 10px 16px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(trim($slot) === '')
                <tr>
                    <td colspan="{{ count($headers) ?: 1 }}" style="text-align: center; padding: 48px; font-size: 14px; color: var(--text-muted);">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @else
                {{-- Note: we expect the slot to contain <tr> elements. We can't easily add hover effects to slotted elements inline without javascript or global css, but we will add a script block to handle the row hover and last-child borders --}}
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('table tbody tr').forEach(row => {
            row.style.borderBottom = '1px solid var(--border-subtle)';
            row.style.transition = 'background 150ms ease';
            
            // Handle hover
            row.addEventListener('mouseenter', () => {
                row.dataset.originalBg = row.style.background;
                row.style.background = 'var(--surface-alt)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.background = row.dataset.originalBg || 'transparent';
            });
            
            // Handle cells
            row.querySelectorAll('td').forEach(cell => {
                cell.style.padding = '12px 16px';
                cell.style.fontSize = '14px';
                cell.style.color = 'var(--text-primary)';
                cell.style.verticalAlign = 'middle';
            });
        });
        
        // Remove border from last rows
        document.querySelectorAll('table tbody').forEach(tbody => {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 0) {
                rows[rows.length - 1].style.borderBottom = 'none';
            }
        });
    });
</script>
