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
            'class_type' => $request->class_type,
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
        $sched->class_type = $request->class_type;
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
    public function destroy($id)
    {
        $sched = Schedule::find($id);
        $sched->delete();
        return response()->json(['msg' => 'successfully removed']);
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
                $obj['contribution_score'] = round($s->contribution_score) ?: null;
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

    private function computeStatus($array){
        $collection = collect($array);
        
        // Step 2: Compute rankings and handle draws
        $ranked = $collection->sortByDesc('rubrick_score');

        $highest = 0;
        $id = null;
        $chk_draw = [];

        $ranked->each(function($obj) use (&$highest, &$id, &$chk_draw){
            if($id == null){
                $highest = $obj['rubrick_score'];
                $id = $obj['id'];
            }else{
                if($highest < $obj['rubrick_score']){
                    $highest = $obj['rubrick_score'];
                    $id = $obj['id'];
                }
            }
            $chk_draw[] = floatval($obj['rubrick_score']);
        });
        info($chk_draw);

        $firstElement = reset($chk_draw); // Get the first element of the array

        $allSame = true; // Assume all elements are the same initially

        foreach ($chk_draw as $element) {
            if ($element !== $firstElement) {
                $allSame = false; // If any element is different from the first one, set $allSame to false
                break; // Exit the loop since we already know they are not all the same
            }
        }

        if ($allSame) {
            $ranked->each(function($obj){
                $s = SchedParticipant::find($obj['id']);
                $s->status = 'draw';
                $s->save();
            });
        } else {
            $ranked->each(function($obj){
                $s = SchedParticipant::find($obj['id']);
                $s->status = 'loss';
                $s->save();
            });
            $s = SchedParticipant::find($id);
            $s->status = 'winner';
            $s->save();
        }

    }

    public function UpdateStatus(Request $request){
        $sched = Schedule::findOrFail($request->sched_id);
        $sched->status = $request->status;
        $sched->save();
        if($sched->status == 'finished'){
            $this->computeStatus($sched->participants);
        }else{
            foreach($sched->participants as $p){
                $p->status = null;
                $p->contribution_score = null;
                $p->save();
            }
        }
        return response()->json(['msg'=> 'success']);
    }

    public function UpdateScore(Request $request){
        $sp = SchedParticipant::findOrFail($request->sched_participant_id);
        $sp->score = number_format($request->score, 2);
        $sp->save();

        $sched = Schedule::findOrFail($sp->sched_id);
        if($sched->status == 'finished'){
            $this->computeStatus($sched->participants);
        }else{
            foreach($sched->participants as $p){
                $p->status = null;
                $p->save();
            }
        }
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
        $query = Schedule::
            where('year', '=', 2024)
            ->where('category_id', '=', $request->id)->get();
        
        $ids = collect($query)
            ->map(function($obj){ return $obj['id']; });
    
        $participants = collect(SchedParticipant::with('info')
            ->whereIn('sched_id', $ids)
            ->get());

        // info($participants);
        return response()->json([
            'participants' => $participants,
            'items' => $query->load('participants')
        ]);
    }

    public function updateScoreContribution(Request $request){
        $p = SchedParticipant::findOrFail($request->id);
        $p->contribution_score = $request->score ?: null;
        $p->save();
        return response()->json(['msg' => 'success']);
    }

    public function getSchedPerDate(Request $request){
        return Schedule::with(['participants_info', 'category'])
        ->whereDate('date_from', '=', $request->date)
        ->orWhereDate('date_to', '=', $request->date)
        ->get();   
    }

    public function getOverall(){
        $p = collect(SchedParticipant::with('info')
            // ->whereNotNull('contribution_score')
            ->leftJoin('participants as p', function($join){
                $join->on('sched_participants.participant_id', '=', 'p.id');
            })
            ->whereNull('p.deleted_at')
            ->selectRaw('participant_id, SUM(contribution_score) as total_score')
            ->groupBy('participant_id')
            ->get())->map(function($obj){
                $item = $obj['info'];
                $item['total_points'] = intval($obj['total_score']);
                return $item;
            })->sortByDesc('total_score');
        return $p;
    }


    public function getCategoriesPerCollege(Request $request){
        $categ = collect(Category::get());
        $items = $categ->map(function($obj) use ($request){
            $total_score = 0;
            $scheds = collect(Schedule::where('category_id', '=', $obj->id)->get())
                ->map(function($item){ return $item->id; });
            if(count($scheds)){
               $total_score = collect(SchedParticipant::whereIn('sched_id', $scheds)
                ->where('participant_id', '=', $request->id)
                ->get())->reduce(function($carry, $item){
                    return $carry + $item["contribution_score"];
                }, 0);
               $score_other = collect(SchedParticipant::whereIn('sched_id', $scheds)
                ->where('participant_id', '!=', $request->id)
                ->selectRaw('participant_id, SUM(contribution_score) as total_score')
                ->groupBy('participant_id')
                ->get())->map(function($obj){
                    return $obj["total_score"];
                })->toArray();

                $filteredArray = array_filter($score_other, function ($value) {
                    return $value !== null;
                });
                $filteredArray[] = $total_score;
                $filteredArray = array_unique($filteredArray);
                rsort($filteredArray);
                
                // Find the rank of $total in the sorted array
                $rank = array_search($total_score, $filteredArray);
                
                // Adjust rank by adding 1 since array_search returns a zero-based index
                if ($rank !== false) {
                    $rank += 1;
                }
            }
            $obj['total_contributions'] = $total_score;
            $obj['rank'] = $total_score ? $rank : "TBA";
            return $obj;
        });

        return $items;
    }


    public function getStandingDashboard(Request $request){
        return RubrickEvaluation::leftJoin('rubrick_insights as ri', function($join){
            $join->on('rubrick_evaluations.insight_id', '=', 'ri.id');
        })->whereNull('ri.deleted_at')
        ->where('sched_id', '=', $request->id)->get();
    }
}
