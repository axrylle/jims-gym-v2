<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
         // Get the total number of users in the membership_user table
         $userCount = DB::table('membership_user')->count();

         // Fetch active and inactive user counts from membership_user table
         $activeUserCount = DB::table('membership_user')->where('status', 1)->count();
         $inactiveUserCount = $userCount - $activeUserCount;
 
         // Calculate the percentage of active users
         $activeUserPercentage = $userCount > 0 ? round(($activeUserCount / $userCount) * 100, 2) : 0;
 
         return [
             Stat::make('Active Members', $activeUserCount)
                 ->description('Number of Active Members')
                 ->descriptionIcon('heroicon-o-check-circle')
                 ->color('success'),
 
             Stat::make('Inactive Members', $inactiveUserCount)
                 ->description('Number of Inactive Members')
                 ->descriptionIcon('heroicon-o-x-circle')
                 ->color('danger'),
 
             Stat::make('Active Members (%)', "{$activeUserPercentage}%")
                 ->description('Percentage of Active Members')
                 ->descriptionIcon('heroicon-o-chart-pie')
                 ->color('info'),
                ];
    }
}
