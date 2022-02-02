<?php

namespace TromsFylkestrafikk\Pto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \TromsFylkestrafikk\Pto\Models\Company
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @mixin \Eloquent
 */
class Company extends Model
{
    protected $table = 'pto_company';
    public $timestamps = false;
    protected $fillable = ['id', 'name'];
}
