<?php

namespace TromsFylkestrafikk\Pto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \TromsFylkestrafikk\Pto\Models\Vehicle
 *
 * @property int $id The vehicle ID as received from SIRI VM dumps
 * @property string|null $internal_id Company internal id of vehicle.
 * @property string $type Vehicle type (bus, hsc, ferry)
 * @property int $company_id
 * @property int $apc_enabled Do we have APC onboard? Is it calibrated enough for everyday use?
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \TromsFylkestrafikk\Pto\Models\VehicleBus|null $bus
 * @property-read \TromsFylkestrafikk\Pto\Models\Company $company
 * @property-read \TromsFylkestrafikk\Pto\Models\VehicleWatercraft|null $watercraft
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereApcEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereInternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vehicle extends Model
{
    public $incrementing = false;
    public $guarded = ['id'];
    protected $table = 'pto_vehicle';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bus()
    {
        return $this->hasOne(VehicleBus::class, 'id');
    }

    public function watercraft()
    {
        return $this->hasOne(VehicleWatercraft::class, 'id');
    }
}
