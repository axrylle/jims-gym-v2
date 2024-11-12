<div>
    <span>{{ \Carbon\Carbon::parse($expiry)->format('M d, Y') }}</span>
    <br>
    <span style="color: #4A5568; font-size: 0.85em;">
        {{ is_numeric($daysRemaining) ? round($daysRemaining) . ' day(s)' : $daysRemaining }}
    </span>
</div>
