<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use HasUuids;

    protected $fillable = [
        'alias',
        'source',
    ];
}
