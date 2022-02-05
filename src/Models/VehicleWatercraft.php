<?php

namespace TromsFylkestrafikk\Pto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \TromsFylkestrafikk\Pto\Models\VehicleWatercraft
 *
 * @property int $id The vehicle ID as received from SIRI VM dumps
 * @property int|null $imo International Maritime Organization number
 * @property string $type
 * @property string $prefix
 * @property string $name
 * @property string|null $callsign
 * @property string|null $phone
 * @property string $line Line numbers separated with comma this vessel primary services.
 * @property int $capacity_pax
 * @property int $capacity_pax_avail
 * @property int|null $capacity_cars
 * @property int|null $capacity_cars_avail
 * @property string|null $url Useful for watercraft geeks in need of more information
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \TromsFylkestrafikk\Pto\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereCallsign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereCapacityCars($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereCapacityCarsAvail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereCapacityPax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereCapacityPaxAvail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereImo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleWatercraft whereUrl($value)
 * @mixin \Eloquent
 */
class VehicleWatercraft extends Model
{
    protected $table = 'pto_vehicle_watercraft';
    public $incrementing = false;
    public $guarded = ['id'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'id');
    }
}
