<?php

namespace Agentcis\Tenant\Model;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $connection = 'agentcis';

    protected $table = 'registration_token';

}
