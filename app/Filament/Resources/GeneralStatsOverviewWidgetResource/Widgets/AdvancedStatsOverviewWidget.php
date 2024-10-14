<?php

namespace App\Filament\Resources\GeneralStatsOverviewWidgetResource\Widgets;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat as AdvancedStatsOverviewWidgetStat;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdvancedStatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $users1 = User::role('admin')->count();
        $users2 = User::role('camera')->count();
        $users3 = User::role('user')->count();
        $users4 = User::role('trader')->count();
        $wImage = Car::whereHas('carImages')->count();
        $woImage = Car::whereDoesntHave('carImages')->count();
        return [
            AdvancedStatsOverviewWidgetStat::make('Admin Users', $users1)
                ->icon('heroicon-o-user')
                ->progress(100)
                ->progressBarColor('success')
                ->iconBackgroundColor('success')
                ->chartColor('success')
                ->iconPosition('start')
                ->descriptionColor('success')
                ->iconColor('success'),
            AdvancedStatsOverviewWidgetStat::make('Camera App Users', $users2)
                ->icon('heroicon-o-user')
                ->progress(100)
                ->progressBarColor('success')
                ->iconBackgroundColor('success')
                ->chartColor('success')
                ->iconPosition('start')
                ->descriptionColor('success')
                ->iconColor('success'),
            AdvancedStatsOverviewWidgetStat::make('Regular Users', $users3)
                ->icon('heroicon-o-user')
                ->progress(100)
                ->progressBarColor('success')
                ->iconBackgroundColor('success')
                ->chartColor('success')
                ->iconPosition('start')
                ->descriptionColor('success')
                ->iconColor('success'),
            AdvancedStatsOverviewWidgetStat::make('Trader Users', $users4)
                ->icon('heroicon-o-user')
                ->progress(100)
                ->progressBarColor('success')
                ->iconBackgroundColor('success')
                ->chartColor('success')
                ->iconPosition('start')
                ->descriptionColor('success')
                ->iconColor('success'),
            AdvancedStatsOverviewWidgetStat::make('Cars with Images', $wImage)
                ->icon('heroicon-o-truck')
                ->progress(100)
                ->progressBarColor('warning')
                ->iconBackgroundColor('warning')
                ->chartColor('warning')
                ->iconPosition('start')
                ->descriptionColor('warning'),
            AdvancedStatsOverviewWidgetStat::make('Cars without Images', $woImage)
                ->icon('heroicon-o-photo')
                ->progress(100)
                ->progressBarColor('primary')
                ->iconBackgroundColor('primary')
                ->chartColor('primary')
                ->iconPosition('start')
                ->descriptionColor('primary'),
        ];
    }
}
