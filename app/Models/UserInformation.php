<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'country', 'city', 'provider_type', 'services', 'description', 'portfolio_link'];
    protected $casts = ['services' => 'array'];
}
