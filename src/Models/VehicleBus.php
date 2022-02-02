<?php

namespace TromsFylkestrafikk\Pto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \TromsFylkestrafikk\Pto\Models\VehicleBus
 *
 * @property int $id The vehicle ID as received from SIRI VM dumps
 * @property string|null $registration_id
 * @property string|null $registration_year
 * @property string|null $brand
 * @property string|null $model
 * @property string $class
 * @property int $capacity_pax
 * @property int $capacity_pax_avail
 * @property int|null $capacity_seats
 * @property int|null $capacity_seats_avail
 * @property int|null $capacity_stands
 * @property int|null $capacity_stands_avail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \TromsFylkestrafikk\Pto\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCapacityPax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCapacityPaxAvail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCapacitySeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCapacitySeatsAvail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCapacityStands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCapacityStandsAvail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereRegistrationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleBus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VehicleBus extends Model
{
    protected $table = 'pto_vehicle_bus';
    public $incrementing = false;
    public $fillable = [
        'id',
        'registration_id',
        'registration_year',
        'brand',
        'model',
        'class',
        'capacity_seats',
        'capacity_stands',
        'capacity_seats_avail',
        'capacity_stands_avail',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'id');
    }
}
