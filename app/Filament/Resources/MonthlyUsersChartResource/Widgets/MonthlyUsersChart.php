<?php

namespace App\Filament\Resources\MonthlyUsersChartResource\Widgets;

use App\Models\Car;
use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;

class MonthlyUsersChart extends AdvancedChartWidget
{
    protected static ?string $heading = '00';
    protected static string $color = 'info';
    protected static ?string $icon = 'heroicon-o-chart-bar';
    protected static ?string $iconColor = 'info';
    protected static ?string $iconBackgroundColor = 'info';
    protected static ?string $label = 'Car Stats';

    public ?string $filter = 'today';

    function __construct()
    {
        self::$heading = Car::count() . ' cars';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        // find cars uploaded based on the filtered timeframes
        $filter = $this->filter;
        if ($filter === 'today') {
            $labels = [
                '00' => '12am',
                '01' => '1am',
                '02' => '2am',
                '03' => '3am',
                '04' => '4am',
                '05' => '5am',
                '06' => '6am',
                '07' => '7am',
                '08' => '8am',
                '09' => '9am',
                '10' => '10am',
                '11' => '11am',
                '12' => '12pm',
                '13' => '1pm',
                '14' => '2pm',
                '15' => '3pm',
                '16' => '4pm',
                '17' => '5pm',
                '18' => '6pm',
                '19' => '7pm',
                '20' => '8pm',
                '21' => '9pm',
                '22' => '10pm',
                '23' => '11pm',
            ];
            $data = [];
            $cars = Car::whereDate('created_at', now())->get()->groupBy(function ($car) {
                return $car->created_at->format('H');
            });
            foreach ($labels as $key => $label) {
                $data[] = @$cars[$key]?->count() ?? 0;
            }
            self::$heading = Car::whereDate('created_at', now())->count() . ' cars';
        } elseif ($filter === 'week') {
            $data = [];
            $labels = [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
            ];
            $cars = Car::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get()->groupBy(function ($car) {
                return $car->created_at->format('l');
            });
            foreach ($labels as $key => $label) {
                $data[] = @$cars[$label]?->count() ?? 0;
            }
            self::$heading = Car::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() . ' cars';
        } elseif ($filter === 'month') {
            $data = [];
            $labels = [
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
                '10',
                '11',
                '12',
                '13',
                '14',
                '15',
                '16',
                '17',
                '18',
                '19',
                '20',
                '21',
                '22',
                '23',
                '24',
                '25',
                '26',
                '27',
                '28',
                '29',
                '30',
                '31',
            ];
            $cars = Car::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->get()->groupBy(function ($car) {
                return $car->created_at->format('j');
            });
            foreach ($labels as $key => $label) {
                $data[] = @$cars[$label]?->count() ?? 0;
            }
            self::$heading = Car::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count() . ' cars';
        } elseif ($filter === 'year') {
            $data = [];
            $labels = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            ];
            $cars = Car::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->get()->groupBy(function ($car) {
                return $car->created_at->format('F');
            });
            foreach ($labels as $key => $label) {
                $data[] = @$cars[$label]?->count() ?? 0;
            }
            self::$heading = Car::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count() . ' cars';
        } else {
            $data = [0];
            $labels = ['No data'];
            self::$heading = '0 cars';
        }
        return [
            'datasets' => [
                [
                    'label' => 'Cars',
                    'data' => $data,
                ],
            ],
            'labels' => array_values($labels),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
