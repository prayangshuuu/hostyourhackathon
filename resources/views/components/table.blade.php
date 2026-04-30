@props(['headers' => [], 'emptyMessage' => 'No data available.'])

<div class="card" style="overflow: hidden; width: 100%; overflow-x: auto;">
    <table class="table" style="text-align: left;">
        <thead style="background: var(--surface-alt); border-bottom: 1px solid var(--border);">
            <tr>
                @foreach($headers as $header)
                    <th style="padding: 10px 16px;">
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
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('table tbody tr').forEach(row => {
            row.style.borderBottom = '1px solid var(--border-subtle)';

            row.querySelectorAll('td').forEach(cell => {
                cell.style.padding = '12px 16px';
                cell.style.fontSize = '14px';
                cell.style.color = 'var(--text-primary)';
                cell.style.verticalAlign = 'middle';
            });
        });
        
        document.querySelectorAll('table tbody').forEach(tbody => {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 0) {
                rows[rows.length - 1].style.borderBottom = 'none';
            }
        });
    });
</script>
