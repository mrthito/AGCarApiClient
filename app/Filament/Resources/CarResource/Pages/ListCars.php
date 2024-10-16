<?php

namespace App\Filament\Resources\CarResource\Pages;

use App\Filament\Resources\CarResource;
use App\Filament\Resources\GeneralStatsOverviewWidgetResource\Widgets\AdvancedStatsOverviewWidget;
use App\Imports\CarImport;
use App\Models\Car;
use App\Models\CarMake;
use App\Models\CarModel;
use Filament\Actions;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListCars extends ListRecords
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // ActionsAction::make('Export')->action('openSettingsModal')->color('danger'),
            ActionsAction::make('Import')->color('success')->form([
                // Select::make('db_classification')
                //     ->required()
                //     ->searchable()
                //     ->options([
                //         'KOREA' => 'Korea',
                //         'YEMEN' => 'Yemen'
                //     ]),
                FileUpload::make('file')
                    ->label('Excel File')
            ])
                ->action(function (array $data): void {
                    $file = $data['file'];
                    $file = storage_path('app/public/' . $data['file']);
                    // dd($file);
                    $excel = Excel::toArray(new CarImport, $file);
                    // dd($excel);
                    $index = 0;
                    if ($excel) {
                        foreach ($excel as $key => $value) {
                            foreach ($value as $key => $row) {
                                if ($index++ > 0) {
                                    $model = CarModel::firstOrCreate([
                                        'name' => $row[5],
                                        'car_make_id' => 1,
                                    ]);
                                    $car = Car::updateOrCreate([
                                        'number' => $row[0],
                                    ], [
                                        // 'db_classification' => $data['db_classification'],
                                        'chasiss_number' => $row[7],
                                        'car_manufacturer' => 1,
                                        'model' => $model->id,
                                        'year' => $row[6],
                                        'color' => $row[8],
                                        'content' => 'N/A',
                                        'status' => 0,
                                        'show_price' => 1,
                                        'price' => $row[12],
                                    ]);
                                }
                            }
                        }
                    }
                }),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            // AdvancedStatsOverviewWidget::class,
        ];
    }
}
