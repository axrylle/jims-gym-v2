<div>
    <span>{{ \Carbon\Carbon::parse($expiry)->format('M d, Y') }}</span>
    <br>
    <span class="text-gray-800 dark:text-white" style="font-size: 0.85em;">
        {{ is_numeric($daysRemaining) ? round($daysRemaining) . ' day(s)' : $daysRemaining }}
    </span>
</div>