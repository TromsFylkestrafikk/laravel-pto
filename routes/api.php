<?php

use Illuminate\Support\Facades\Route;
use TromsFylkestrafikk\Pto\Http\Resources\VehicleResource;
use TromsFylkestrafikk\Pto\Models\Vehicle;

Route::get('vehicle/{vehicle}', function (Vehicle $vehicle) {
    return new VehicleResource($vehicle);
});
