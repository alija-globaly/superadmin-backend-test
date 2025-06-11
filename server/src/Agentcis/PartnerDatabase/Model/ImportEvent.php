<?php

namespace Agentcis\PartnerDatabase\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ImportEvent extends Model
{
    public $incrementing = false;
    protected $table = 'import_log';
    protected $keyType = 'string';
    protected $casts = [
        'report' => 'json'
    ];

    protected $fillable = [
        'file',
        'user_id',
        'status',
        'report'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
