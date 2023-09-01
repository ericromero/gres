<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Space extends Model
{
    use HasFactory;

    // Aquí puedes definir los atributos que sean "fillable" o asignables en masa.
    protected $fillable = [
        'name',
        'location',
        'capacity',
        'availability',
        'department_id',
    ];

    // Relación con el modelo Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relación del espacio con los horarios disponibles para su uso
    public function availability(): HasMany
    {
        return $this->hasMany(SpaceAvailability::class);
    }
}
