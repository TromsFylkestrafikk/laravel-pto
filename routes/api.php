<?php

use Illuminate\Support\Facades\Route;
use TfkSamf\Pto\Http\Resources\VehicleResource;
use TfkSamf\Pto\Models\Vehicle;

Route::get('vehicle/{vehicle}', function (Vehicle $vehicle) {
    return new VehicleResource($vehicle);
});
