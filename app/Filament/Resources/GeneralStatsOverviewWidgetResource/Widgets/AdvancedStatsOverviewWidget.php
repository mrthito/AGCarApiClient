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
        $users = User::count();
        $cars = Car::count();
        $image = CarImage::count();
        return [
            AdvancedStatsOverviewWidgetStat::make('Total Users', $users)
                ->icon('heroicon-o-user')
                ->progress(100)
                ->progressBarColor('success')
                ->iconBackgroundColor('success')
                ->chartColor('success')
                ->iconPosition('start')
                ->descriptionColor('success')
                ->iconColor('success'),
            AdvancedStatsOverviewWidgetStat::make('Total Cars', $cars)
                ->icon('heroicon-o-truck')
                ->progress(100)
                ->progressBarColor('warning')
                ->iconBackgroundColor('warning')
                ->chartColor('warning')
                ->iconPosition('start')
                ->descriptionColor('warning'),
            AdvancedStatsOverviewWidgetStat::make('Images', $image)
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
