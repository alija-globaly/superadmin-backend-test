<?php
namespace Agentcis\PartnerDatabase\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    const HEAD_OFFICE = 'Head Office';
    const OTHER_OFFICE = 'Other Office';

    const TYPES = [
        self::HEAD_OFFICE,
        self::OTHER_OFFICE,
    ];
    protected $table = 'partner_branches';
    protected $fillable= [
        'name',
        'email',
        'phone_number',
        'city',
        'state',
        'zip_code',
        'street',
        'country',
        'type'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }
}
