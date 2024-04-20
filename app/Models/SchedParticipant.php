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
        'sched_id', 'participant_id', 'score', 'status', 'contribution_score'
    ];

    protected $appends = ['rubrick_score'];

    public function info(){
        return $this->hasOne(Participant::class, 'id', 'participant_id');
    }

    public function getRubrickScoreAttribute(){
        $sched = Schedule::findOrFail($this->sched_id);
        if($sched->rubrick_id){
            if($sched->status != 'finished'){
                return -1;
            }
            return number_format(collect(RubrickEvaluation::where('sched_id', '=', $this->sched_id)
            ->leftJoin('rubrick_insights as ri', function($join){
                $join->on('ri.id', '=', 'rubrick_evaluations.insight_id');
            })
            ->whereNull('ri.deleted_at')
            ->where('participant_id', '=', $this->participant_id)
            ->get())
            ->map(function($obj){
                return $obj->eval_score;
            })->avg(), 2);
        }else{
            return $this->score;
        }
    }
}
