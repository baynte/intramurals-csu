<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Participant;
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
                $obj['score'] = round($s->score) ?: 0;
                return $obj;
            });
        return Inertia::render('Admin/Standing', [
            'category' => $categ,
            'participants' => $participants,
            'sched' => $sched,
        ]);
    }
    public function privateStanding(Request $request){
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
                $obj['score'] = round($s->score) ?: 0;
                return $obj;
            });
        return Inertia::render('UpdateStanding', [
            'category' => $categ,
            'participants' => $participants,
            'sched' => $sched,
        ]);
    }
}
