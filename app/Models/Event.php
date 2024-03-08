<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    // Aquí puedes definir los atributos que sean "fillable" o asignables en masa.
    protected $fillable = [
        'responsible_id',
        'corresponsible_id',
        'requirements',
        'department_id',
        'event_type_id',
        'title',
        'contact_email',
        'website',
        'summary',
        'program',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'audience',
        'modality',
        'scope',
        'project_type',
        'gender_equality',
        'knowledge_area',
        'cover_image',
        'user_id',
        'number_of_attendees',
        'registration_required',
        'registration_url',
        'space_required',
        'transmission_required',
        'recording_required',
        'status',
        'published',
        'published_by',
        'cancelled',
        'register_id',
    ];

    protected $dates = ['start_date', 'end_date'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function coresponsible()
    {
        return $this->belongsTo(User::class, 'coresponsible_id');
    }

    public function canceledEvent()
    {
        return $this->hasOne(CanceledEvent::class, 'event_id');
    }

    public function isAvailable()
    {
        return $this->status === 'Aceptado' && $this->space->events()->where(function ($query) {
            $query->whereBetween('start_date', [$this->start_date_time, $this->end_date_time])
                  ->orWhereBetween('end_date', [$this->start_date_time, $this->end_date_time])
                  ->orWhere(function ($query) {
                      $query->where('start_date', '<=', $this->start_date_time)
                            ->where('end_date', '>=', $this->end_date_time);
                  });
        })->count() === 0;
    }

    public function getStartDateAndTimeAttribute()
    {
        return $this->start_date . ' ' . $this->start_time;
    }

    public function getEndDateAndTimeAttribute()
    {
        return $this->end_date . ' ' . $this->end_time;
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'event_spaces', 'event_id', 'space_id')->withPivot(['status','observation']);
    }

    public function eventRecordings()
    {
        return $this->hasMany(EventRecording::class);
    }

    public function eventStreamings()
    {
        return $this->hasMany(EventStreaming::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_participants');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('participation_type_id', 'other_participation');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function audience()
    {
        return $this->belongsTo(Audience::class,'audience_id');
    }

    public function knowledgeArea()
    {
        return $this->belongsTo(KnowledgeArea::class, 'knowledge_area_id');
    }

    public function eventSpaces()
    {
        return $this->hasMany(EventSpace::class, 'event_id');
    }

}
