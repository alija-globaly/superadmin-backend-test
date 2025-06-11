<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use League\OAuth2\Server\Exception\OAuthServerException;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $casts = [
        'status' => 'bool'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->getAttribute('role'), $roles);
    }

    public function validateForPassportPasswordGrant()
    {
        if ($this->status) {
            return true;
        }
        throw new OAuthServerException('User account is not active', 6, 'account_inactive', 401);
    }
}
