<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Participant;
use App\Models\Rubrick;
use App\Models\RubrickEvaluation;
use App\Models\SchedAuthor;
use App\Models\SchedParticipant;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Schedule::with('participants')
            ->where('year', '=', $request->year)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sched = Schedule::create([
            'status' => 'pending',
            'category_id' => $request->category_id,
            'year' => $request->year,
            'date_from' => $request->date_from, 
            'date_to' => $request->date_to, 
            'time' => $request->time, 
            'venue' => $request->venue,
            'description' => $request->description, 
        ]);
        
        foreach($request->participants_id as $participant){
            SchedParticipant::create([
                'sched_id' => $sched->id,
                'participant_id' => $participant,
                'score' => 0
            ]);
        }

        return response()->json(['msg' => 'success', 'item' => $sched->load('participants')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sched = Schedule::findOrFail($id);
        $sched->category_id = $request->category_id;
        $sched->year = $request->year;
        $sched->date_from = $request->date_from; 
        $sched->date_to = $request->date_to; 
        $sched->time = $request->time; 
        $sched->venue = $request->venue;
        $sched->description = $request->description;
        $sched->save();

        SchedParticipant::where('sched_id', '=', $sched->id)->delete();
        
        foreach($request->participants_id as $participant){
            SchedParticipant::create([
                'sched_id' => $sched->id,
                'participant_id' => $participant,
                'score' => 0
            ]);
        }

        return response()->json(['msg' => 'success', 'item' => $sched->load('participants')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }

    public function editStanding(Request $request){
        $sched = Schedule::findOrFail($request->id);
        $categ = Category::findOrFail($sched->category_id);
        $p_id = collect($sched->participants)->map(function($obj){
            return $obj->participant_id;
        });

        $participants = collect(Participant::whereIn('id', $p_id)->orderBy('name', 'asc')->get())
            ->map(function($obj) use ($sched){
                $s = SchedParticipant::where('participant_id','=', $obj['id'])
                    ->where('sched_id', '=', $sched->id)
                    ->first();
                $obj['sched_participant_id'] = $s->id;
                $obj['score'] = round($s->score) ?: 0;
                return $obj;
            });
            return Inertia::render('Admin/Standing', [
            'category' => $categ,
            'participants' => $participants,
            'sched' => $sched,
            'rubricks' => Rubrick::with('insights')->where('year', '=', $sched->year)->get(),
            'authors' => SchedAuthor::where('sched_id', '=', $sched->id)->get()
        ]);
    }
    public function privateStanding(Request $request){
        $author = SchedAuthor::findOrFail($request->author_id);
        $sched = Schedule::findOrFail($request->id);
        if($sched->status == 'finished'){
            return response('Already Finished', 404);
        }
        $categ = Category::findOrFail($sched->category_id);
        $p_id = collect($sched->participants)->map(function($obj){
            return $obj->participant_id;
        });
        
        $participants = collect(Participant::whereIn('id', $p_id)->orderBy('name', 'asc')->get())
        ->map(function($obj) use ($sched){
            $s = SchedParticipant::where('participant_id','=', $obj['id'])
                ->where('sched_id', '=', $sched->id)
                ->first();
                    $obj['sched_participant_id'] = $s->id;
                    $obj['score'] = round($s->score) ?: 0;
                    return $obj;
                });
        return Inertia::render('UpdateStanding', [
            'category' => $categ,
            'participants' => $participants,
            'sched' => $sched,
            'rubricks' => Rubrick::with('insights')->where('year', '=', $sched->year)->get(),
            'author' => $author,
        ]);
    }

    public function UpdateStatus(Request $request){
        $sched = Schedule::findOrFail($request->sched_id);
        $sched->status = $request->status;
        $sched->save();
        return response()->json(['msg'=> 'success']);
    }

    public function UpdateScore(Request $request){
        $sp = SchedParticipant::findOrFail($request->sched_participant_id);
        $sp->score = number_format($request->score, 2);
        $sp->save();
        return response()->json(['msg'=>'updated']);
    }

    public function UpdateAuthScore(Request $request){
        RubrickEvaluation::where('participant_id', '=', $request->participant_id)
            ->where('auth_id', '=', $request->auth_id)
            ->where('sched_id', '=', $request->sched_id)
            ->delete();
        foreach($request->evaluations as $eval){
            RubrickEvaluation::create([
                'sched_id' => $request->sched_id, 
                'auth_id' => $request->auth_id, 
                'participant_id' => $request->participant_id, 
                'insight_id' => $eval['id'], 
                'eval_score' => $eval['score']
            ]);
        }
        return redirect()->back();
    }

    public function getSelectedDate(Request $request){
        $items = Schedule::with(['participants_info', 'category'])
            ->whereDate('date_from', '=', $request->date)
            ->orWhereDate('date_to', '=', $request->date)
            ->get();

        return $items;
    }
    
    public function getDtEvents(Request $request){
        $ids = collect(Schedule::where('category_id', '=', $request->id)->get())
            ->map(function($obj){ return $obj['id']; });
        $items = SchedParticipant::with('info')
            ->whereIn('sched_id', $ids)
            ->get();

        return $items;
    }
}
