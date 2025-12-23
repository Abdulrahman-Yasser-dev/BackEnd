<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'category',
        'work_title',
        'work_description',
        'service_type',
        'city',
        'phone',
        'expected_date',
        'file_attachments',
    ];

    protected $casts = [
        'file_attachments' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
