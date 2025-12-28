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
        'work_title',
        'work_description',
        'service_type',
        'city',
        'phone',
        'expected_date',
        'duration',
        'budget_min',
        'budget_max',
        'category_ids',
        'skill_ids',
        'file_attachments',
        'status',
        'pending_status',
        'pending_status_changed_by',
        'provider_id',
    ];

    protected $casts = [
        'file_attachments' => 'array',
        'category_ids' => 'array',
        'skill_ids' => 'array',
        'expected_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function logs()
    {
        return $this->hasMany(WorkRequestLog::class);
    }
}
