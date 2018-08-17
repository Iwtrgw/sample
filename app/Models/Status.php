<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];

    //与用户表关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
