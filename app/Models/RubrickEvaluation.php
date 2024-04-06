<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RubrickEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sched_id', 'participant_id', 'insight_id', 'eval_score', 'auth_id'
    ];
}
