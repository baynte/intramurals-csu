<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'status', 'date_from', 'date_to', 'time', 'venue',
        'description', 'category_id', 'year', 'rubrick_id'
    ];

    public function participants(){
        return $this->hasMany(SchedParticipant::class, 'sched_id', 'id');
    }

    public function participants_info(){
        return $this->hasManyThrough(Participant::class, SchedParticipant::class, 'sched_id', 'id', 'id', 'participant_id');
    }

    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
