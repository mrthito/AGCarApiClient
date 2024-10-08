<?php

namespace App\Exports;

use App\Models\Car;
use Maatwebsite\Excel\Concerns\FromArray;

class CarExport implements FromArray
{
    public function __construct(public $ids) {}
    public function array(): array
    {
        $header = [
            "고유번호(수정X)",
            "선적넘버",
            "BUYER",
            "A/S",
            "DATE(0000-00-00)",
            "MODEL",
            "YEAR",
            "CH",
            "COLOR",
            "COMPANY",
            "TELEPHONE",
            "매입금액(숫자만입력)",
            "TOTAL MONEY(숫자만입력)",
            "KOREA TOTAL $(숫자만입력)",
            "SHIP $(숫자만입력)",
            "CUSTOM $(숫자만입력)",
            "FIXING $(숫자만입력)",
            "YEMEN TOTAL $(자동계산)",
            "SALE $(숫자만입력)",
            "BALANCE $(자동계산)",
            "A/B",
            "DEPOSIT $(숫자만입력)",
            "SALES DATE(0000-00-00)",
            "CITY",
            "CONSULT(0000-00-00)"
        ];
        $cars = Car::whereIn('id', $this->ids)->get()->map(function ($car) {
            return [
                $car->number,
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                $car->model?->name ?? 'N/A',
                $car->year,
                $car->chasiss_number,
                $car->color,
                'N/A',
                'N/A',
                'N/A',
                $car->price,
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
            ];
        });

        // dd($cars->toArray());
        return $cars->prepend($header)->toArray();
    }
}
