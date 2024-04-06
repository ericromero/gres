<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceException extends Model
{
    use HasFactory;

    protected $fillable = ['space_id', 'day_of_week', 'start_time', 'end_time'];

}
