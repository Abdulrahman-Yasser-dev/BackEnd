<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'icon', 'type'];

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'category_user');
    }
}
