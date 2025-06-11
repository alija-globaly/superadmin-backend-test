<?php

namespace Agentcis\PartnerDatabase\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Partner extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'fax',
        'website',
        'category_id',
        'street',
        'city',
        'state',
        'zip_code',
        'country',
        'currency_code',
        'registration_number',
    ];


    public static function uuid(string $uuid): ?Partner
    {
        return static::query()->where('uuid', $uuid)->first();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function branches()
    {
        return $this->hasMany(Branch::class)->with('country');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
