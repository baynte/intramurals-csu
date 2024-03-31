<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchedParticipant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sched_id', 'participant_id', 'score'
    ];
}
