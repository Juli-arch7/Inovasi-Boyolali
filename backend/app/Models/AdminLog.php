<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_admin',
        'action',
        'target_id',
        'target_type',
        'description',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}
