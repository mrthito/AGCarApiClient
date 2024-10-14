<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarMake;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class VehicleController extends Controller
{

    /**
     * Display a paginated listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $cars = Car::with(['carManufacturer', 'carModel'])
            ->when($request->has('search') && $request->input('search') != '', function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('db_classification', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('chasiss_number', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('car_manufacturer', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('model', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('year', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('color', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('fuel_type', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('number', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('content', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('status', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('show_price', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('price', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->where('status_camera', 1)
            ->paginate($perPage);
        $newCars = $cars->map(function ($car) {
            return [
                'id' => $car->id,
                'image' => $car->image ? asset('storage/' . $car->image) : null,
                'db_classification' => $car->db_classification,
                'chasiss_number' => $car->chasiss_number,
                'car_manufacturer' => $car->carManufacturer->name,
                'model' => $car->carModel->name,
                'year' => $car->year,
                'color' => $car->color,
                'fuel_type' => $car->fuel_type,
                'number' => $car->number,
                'content' => $car->content,
                'status' => $car->status,
                'show_price' => $car->show_price,
                'price' => $car->price,

                'buyer' => $car->buyer,
                'buying_date' => $car->buying_date,
                'company_source' => $car->company_source,
                'korean_price' => $car->korean_price,
                'price_in_dollar' => $car->price_in_dollar,
                'shipping_price' => $car->shipping_price,
                'custom_price' => $car->custom_price,
                'fixing_price' => $car->fixing_price,
                'total_cost' => $car->total_cost,
                'city' => $car->city,
                'arrival_date' => $car->arrival_date,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $newCars,
            'total' => $cars->total(),
            'per_page' => $cars->perPage(),
            'current_page' => $cars->currentPage(),
            'next' => $cars->nextPageUrl(),
            'prev' => $cars->previousPageUrl(),
        ], 200);
    }
    public function indexNew(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $cars = Car::with(['carManufacturer', 'carModel'])
            ->when($request->has('search') && $request->input('search') != '', function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('db_classification', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('chasiss_number', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('car_manufacturer', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('model', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('year', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('color', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('fuel_type', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('number', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('content', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('status', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('show_price', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('price', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->where('status', 1)
            ->paginate($perPage);
        $newCars = $cars->map(function ($car) {
            return [
                'id' => $car->id,
                'image' => $car->image ? asset('storage/' . $car->image) : null,
                'db_classification' => $car->db_classification,
                'chasiss_number' => $car->chasiss_number,
                'car_manufacturer' => $car->carManufacturer->name,
                'model' => $car->carModel->name,
                'year' => $car->year,
                'color' => $car->color,
                'fuel_type' => $car->fuel_type,
                'number' => $car->number,
                'content' => $car->content,
                'status' => $car->status,
                'show_price' => $car->show_price,
                'price' => $car->price,

                'buyer' => $car->buyer,
                'buying_date' => $car->buying_date,
                'company_source' => $car->company_source,
                'korean_price' => $car->korean_price,
                'price_in_dollar' => $car->price_in_dollar,
                'shipping_price' => $car->shipping_price,
                'custom_price' => $car->custom_price,
                'fixing_price' => $car->fixing_price,
                'total_cost' => $car->total_cost,
                'city' => $car->city,
                'arrival_date' => $car->arrival_date,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $newCars,
            'total' => $cars->total(),
            'per_page' => $cars->perPage(),
            'current_page' => $cars->currentPage(),
            'next' => $cars->nextPageUrl(),
            'prev' => $cars->previousPageUrl(),
        ], 200);
    }

    function single(Request $request, $id)
    {
        $vehicle = Car::with('carImages', 'carManufacturer', 'carModel')->findOrFail($id);
        $data = [
            'id' => $vehicle->id,
            'image' => $vehicle->image ? asset('storage/' . $vehicle->image) : null,
            'db_classification' => $vehicle->db_classification,
            'chasiss_number' => $vehicle->chasiss_number,
            'car_manufacturer' => $vehicle->carManufacturer->name,
            'model' => $vehicle->carModel->name,
            'year' => $vehicle->year,
            'color' => $vehicle->color,
            'fuel_type' => $vehicle->fuel_type,
            'number' => $vehicle->number,
            'content' => $vehicle->content,
            'status' => $vehicle->status,
            'show_price' => $vehicle->show_price,
            'price' => $vehicle->price,
            'buyer' => $vehicle->buyer,
            'buying_date' => $vehicle->buying_date,
            'company_source' => $vehicle->company_source,
            'korean_price' => $vehicle->korean_price,
            'price_in_dollar' => $vehicle->price_in_dollar,
            'shipping_price' => $vehicle->shipping_price,
            'custom_price' => $vehicle->custom_price,
            'fixing_price' => $vehicle->fixing_price,
            'total_cost' => $vehicle->total_cost,
            'city' => $vehicle->city,
            'arrival_date' => $vehicle->arrival_date,
            'images' => $vehicle->carImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image ? asset('storage/' . $image->image) : null,
                ];
            })
        ];
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    function images(Request $request, $id)
    {
        $vehicle = Car::findOrFail($id);
        $images = $request->images;

        if ($images) {
            $index = 0;
            foreach ($images as $image) {
                $makeImage = $this->base64Image($image);
                if ($index == 0) {
                    $vehicle->image = $makeImage;
                    $vehicle->save();
                } else {
                    CarImage::create([
                        'car_id' => $vehicle->id,
                        'image' => $makeImage
                    ]);
                }
                $index++;
            }

            return response()->json(['status' => 'success', 'message' => 'Images uploaded successfully'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'No image uploaded'], 400);
    }

    function base64Image($image)
    {
        $file = '/images/' . Uuid::uuid4()->toString() . '.png';
        $image_parts = explode(";base64,", $image);
        if (count($image_parts) == 1) {
            $image_base64 = base64_decode($image_parts[0]);
        } else {
            $image_base64 = base64_decode($image_parts[1]);
        }
        Storage::disk('public')->put($file, $image_base64);
        return $file;
    }

    /**
     * Display the specified vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $vehicle = Car::findOrFail($id);

        // Check if the vehicle exists
        if ($vehicle) {
            return response()->json(['status' => 'success', 'data' => $vehicle], 200);
        }

        // If the vehicle does not exist
        return response()->json(['status' => 'error', 'message' => 'Car not found'], 404);
    }

    /**
     * Display a similar car listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function similarCars(Request $request, $id)
    {
        // $subject = $request->input('name');
        $vehicle = Car::findOrFail($id);
        $role = $request->input('role');
        // all similar vehicle data
        $cars = Car::where('status', 1)
            ->where('id', '<>', $vehicle->id)
            ->where(function ($query) use ($vehicle) {
                $query->where('car_manufacturer', $vehicle->car_manufacturer)
                    ->where('model', $vehicle->model);
            })
            ->limit(5)
            ->get();

        return response()->json(['status' => 'success', 'data' => $cars], 200);
    }

    /**
     * Display a search car listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        // all search vehicle data
        $cars = Car::select('wr_id', 'wr_num', 'ca_name', 'wr_option', 'model', 'wr_3', 'wr_4', 'wr_5', 'wr_8', 'wr_7', 'wr_9', 'wr_11', 'wr_34', 'wr_car_manufacturer', 'wr_car_price', 'wr_car_price_display_status', 'wr_car_car_display_status', 'wr_car_fuel_type')->where('model', $name)->get();

        return response()->json(['status' => 'success', 'data' => $cars], 200);
    }

    /**
     * Display a filter search car listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterSearch(Request $request)
    {
        // Retrieve filter inputs from the request
        $year = $request->input('year');
        $manufacturer = $request->input('manufacturer');
        $model = $request->input('model');
        $role = $request->input('role');
        // Start query builder
        $vehiclesQuery = Car::select('wr_id', 'wr_num', 'ca_name', 'wr_option', 'model', 'wr_3', 'wr_4', 'wr_5', 'wr_8', 'wr_7', 'wr_9', 'wr_11', 'wr_34', 'wr_car_manufacturer', 'wr_car_price', 'wr_car_price_display_status', 'wr_car_car_display_status', 'wr_car_fuel_type');

        // Apply filters if provided
        if ($year) {
            $vehiclesQuery->where('wr_3', $year);
        }

        if ($manufacturer) {
            $vehiclesQuery->where('wr_car_manufacturer', $manufacturer);
        }

        if ($model) {
            $vehiclesQuery->where('model', $model);
        }

        if ($role == 'user') {
            $vehiclesQuery = $vehiclesQuery->where('wr_car_price_display_status', 1);
        }
        if ($role == 'trader') {
            $vehiclesQuery = $vehiclesQuery->where('wr_car_price_display_status', 2);
        }

        // Execute the query and get the results
        $cars = $vehiclesQuery->get();

        // Return the response with the data
        return response()->json(['status' => 'success', 'data' => $cars], 200);
    }

    /**
     * Display a manufacturer listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function manufacturer(Request $request)
    {
        $manufacturers = CarMake::select('id', 'name')->get();
        return response()->json(['status' => 'success', 'data' => $manufacturers], 200);
    }

    /**
     * Display a model listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function model(Request $request)
    {
        $models = CarModel::get()->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'manufacturer' => $model->carMake->name,
            ];
        });
        return response()->json(['status' => 'success', 'data' => $models], 200);
    }

    /**
     * Display a getModelByManufacturer listing of the cars.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getModelByManufacturer(Request $request, $manufacturer)
    {
        $models = CarModel::where('car_make_id', $manufacturer)->get()->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'manufacturer' => $model->carMake->name,
            ];
        });
        return response()->json(['status' => 'success', 'data' => $models], 200);
    }
}
