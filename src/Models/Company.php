<?php

namespace TfkSamf\Pto\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \TfkSamf\Pto\Models\Company
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
    public $incrementing = false;
    protected $table = 'pto_company';
    protected $guarded = ['id'];
}
