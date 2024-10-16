<?php

namespace App\Filament\Resources;

use App\Exports\CarExport;
use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers;
use App\Filament\Resources\CarResource\RelationManagers\CarRelationManager;
use App\Filament\Resources\GeneralStatsOverviewWidgetResource\Widgets\AdvancedStatsOverviewWidget;
use App\Models\Car;
use App\Models\CarMake;
use App\Models\CarModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component as Livewire;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Car Cover Image')
                    ->image()
                    ->required(),
                Forms\Components\Select::make('db_classification')
                    ->required()
                    ->searchable()
                    ->options([
                        'KOREA' => 'Korea',
                        'YEMEN' => 'Yemen'
                    ]),
                Forms\Components\TextInput::make('chasiss_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('car_manufacturer')
                    ->required()
                    ->searchable()
                    ->options(
                        CarMake::all()->pluck('name', 'id')->toArray()
                    )
                    ->live()
                    ->placeholder('Select Car Manufacturer'),
                Forms\Components\Select::make('model')
                    ->searchable()
                    ->required()
                    ->disabled(fn(Forms\Get $get): bool => ! filled($get('car_manufacturer')))
                    ->options(function (Forms\Get $get) {
                        return CarMake::find($get('car_manufacturer'))?->carModels->pluck('name', 'id')->toArray() ?? [];
                    }),
                Forms\Components\Select::make('year')
                    ->required()
                    ->searchable()
                    ->options(
                        // range(2000, 2022) with keys and values reversed
                        array_combine(range(date('Y'), 1900), range(date('Y'), 1900))
                    ),
                Forms\Components\Select::make('color')
                    ->required()
                    ->searchable()
                    ->options([
                        'ORANGE' => 'ORANGE',
                        'PINK' => 'PINK',
                        'Paige' => 'Paige',
                        'Pink' => 'Pink',
                        'RED' => 'RED',
                        'Read' => 'Read',
                        'Red' => 'Red',
                        'SELFER' => 'SELFER',
                        'SILVER' => 'SILVER',
                        'SKY' => 'SKY',
                        'BLUE' => 'BLUE',
                        'Silver' => 'Silver',
                        'Vibrani' => 'Vibrani',
                        'WHITE' => 'WHITE',
                        'WINE' => 'WINE',
                        'White' => 'White',
                        'beige' => 'beige',
                        'blue' => 'blue',
                        'gray' => 'gray',
                        'lead' => 'lead',
                        'orange' => 'orange',
                        'orenge' => 'orenge',
                        'pink' => 'pink',
                        'red' => 'red',
                        'silver' => 'silver',
                    ]),
                Forms\Components\Select::make('fuel_type')
                    ->required()
                    ->searchable()
                    ->options([
                        'Diesel' => 'Diesel',
                        'Gasoline' => 'Gasoline',
                        'Hybrid' => 'Hybrid',
                        'LPG' => 'LPG',
                        'EV' => 'EV',
                    ]),
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->label('Unique Number')
                    ->hiddenOn(['edit'])
                    ->unique()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required(),
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('buyer')
                    ->required(),
                Forms\Components\DatePicker::make('buying_date')
                    ->displayFormat('Y-m-d')
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('show_to')
                    ->required()
                    ->options([
                        'all' => 'All',
                        'user' => 'User',
                        'trader' => 'Trader',
                    ]),
                Forms\Components\TextInput::make('company_source')
                    ->required(),
                Forms\Components\TextInput::make('korean_price')
                    ->numeric()
                    ->prefix('WON')
                    ->required(),
                Forms\Components\TextInput::make('price_in_dollar')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('shipping_price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('custom_price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('fixing_price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('total_cost')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('city')
                    ->required(),
                Forms\Components\DatePicker::make('arrival_date')
                    ->displayFormat('Y-m-d')
                    ->native(false)
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->label('Show this car on mobile app')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('status_camera')
                    ->label('Show this car on camera app')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('show_price')
                    ->label('Show Price on mobile app')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('db_classification')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('buying_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('carModel.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('chasiss_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_source')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('korean_price')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('price_in_dollar')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_price')
                    ->sortable()
                    ->prefix('$')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('custom_price')
                    ->sortable()
                    ->prefix('$')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fixing_price')
                    ->sortable()
                    ->prefix('$')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->sortable()
                    ->prefix('$')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('arrival_date')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('carManufacturer.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fuel_type')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\SelectColumn::make('show_to')
                    ->options([
                        'all' => 'All',
                        'user' => 'User',
                        'trader' => 'Trader',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Show on mobile app')
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('status_camera')
                    ->label('Show on camera app')
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('show_price')
                    ->label('Show Price on mobile app')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('db_classification')
                            ->required()
                            ->searchable()
                            ->options([
                                'KOREA' => 'Korea',
                                'YEMEN' => 'Yemen'
                            ]),
                        Forms\Components\TextInput::make('chasiss_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('car_manufacturer')
                            ->required()
                            ->searchable()
                            ->options(
                                CarMake::all()->pluck('name', 'id')->toArray()
                            )
                            ->live()
                            ->placeholder('Select Car Manufacturer'),
                        Forms\Components\Select::make('model')
                            ->searchable()
                            ->required()
                            ->disabled(fn(Forms\Get $get): bool => ! filled($get('car_manufacturer')))
                            ->options(function (Forms\Get $get) {
                                return CarMake::find($get('car_manufacturer'))?->carModels->pluck('name', 'id')->toArray() ?? [];
                            }),
                        Forms\Components\Select::make('year')
                            ->required()
                            ->searchable()
                            ->options(
                                array_combine(range(date('Y'), 1900), range(date('Y'), 1900))
                            ),
                        Forms\Components\Select::make('color')
                            ->required()
                            ->searchable()
                            ->options([
                                'ORANGE' => 'ORANGE',
                                'PINK' => 'PINK',
                                'Paige' => 'Paige',
                                'Pink' => 'Pink',
                                'RED' => 'RED',
                                'Read' => 'Read',
                                'Red' => 'Red',
                                'SELFER' => 'SELFER',
                                'SILVER' => 'SILVER',
                                'SKY' => 'SKY',
                                'BLUE' => 'BLUE',
                                'Silver' => 'Silver',
                                'Vibrani' => 'Vibrani',
                                'WHITE' => 'WHITE',
                                'WINE' => 'WINE',
                                'White' => 'White',
                                'beige' => 'beige',
                                'blue' => 'blue',
                                'gray' => 'gray',
                                'lead' => 'lead',
                                'orange' => 'orange',
                                'orenge' => 'orenge',
                                'pink' => 'pink',
                                'red' => 'red',
                                'silver' => 'silver',
                            ]),
                        Forms\Components\Select::make('fuel_type')
                            ->required()
                            ->searchable()
                            ->options([
                                'Diesel' => 'Diesel',
                                'Gasoline' => 'Gasoline',
                                'Hybrid' => 'Hybrid',
                                'LPG' => 'LPG',
                                'EV' => 'EV',
                            ]),
                        Forms\Components\TextInput::make('number')
                            ->required()
                            ->numeric()
                            ->maxLength(255),
                        // Forms\Components\TextInput::make('price')
                        //     ->numeric()
                        //     ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['db_classification'], fn(Builder $query, $value) => $query->where('db_classification', $value))
                            ->when($data['chasiss_number'], fn(Builder $query, $value) => $query->where('chasiss_number', 'like', "%$value%"))
                            ->when($data['car_manufacturer'], fn(Builder $query, $value) => $query->where('car_manufacturer', $value))
                            ->when($data['model'], fn(Builder $query, $value) => $query->where('model', $value))
                            ->when($data['year'], fn(Builder $query, $value) => $query->where('year', $value))
                            ->when($data['color'], fn(Builder $query, $value) => $query->where('color', $value))
                            ->when($data['fuel_type'], fn(Builder $query, $value) => $query->where('fuel_type', $value))
                            ->when($data['number'], fn(Builder $query, $value) => $query->where('number', 'like', "%$value%"));
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $selectedIds = $records->pluck('id')->toArray();
                            return Excel::download(new CarExport($selectedIds), 'export' . date('Y-m-d') . '.xlsx');
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    BulkAction::make('status_on')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->label('Show on mobile app')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $selectedIds = $records->pluck('id')->toArray();
                            Car::whereIn('id', $selectedIds)->update(['status' => 1]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    BulkAction::make('status_off')
                        ->icon('heroicon-o-eye-slash')
                        ->label('Hide on mobile app')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $selectedIds = $records->pluck('id')->toArray();
                            Car::whereIn('id', $selectedIds)->update(['status' => 0]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    BulkAction::make('show_price')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->label('Show Price on mobile app')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $selectedIds = $records->pluck('id')->toArray();
                            Car::whereIn('id', $selectedIds)->update(['show_price' => 1]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    BulkAction::make('hide_price')
                        ->icon('heroicon-o-eye-slash')
                        ->label('Hide Price on mobile app')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $selectedIds = $records->pluck('id')->toArray();
                            Car::whereIn('id', $selectedIds)->update(['show_price' => 0]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CarRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
