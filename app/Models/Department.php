<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'logo','description','institution_id', 'responsible_id'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }


    public function adscriptions()
    {
        return $this->hasMany(Adscription::class);
    }

    public function spaces()
    {
        return $this->hasMany(Space::class, 'department_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
