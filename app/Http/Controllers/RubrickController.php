<?php

namespace App\Http\Controllers;

use App\Models\Rubrick;
use App\Models\RubrickInsight;
use App\Models\Schedule;
use Illuminate\Http\Request;

class RubrickController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Rubrick::with('insights')
            ->where('year', '=', $request->year)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rub = Rubrick::create([
            'title' => $request->title,
            'year' => $request->year,
        ]);

        foreach($request->categories as $item){
            RubrickInsight::create([
                'rubrick_id' => $rub->id,
                'value' => $item
            ]);
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Rubrick $rubrick)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rubrick $rubrick)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rubrick $rubrick)
    {
        //
    }

    public function updateRubrickType($id, Request $request){
        $s = Schedule::findOrFail($id);
        $s->rubrick_id = $request->rubrick_id;
        $s->save();
        return redirect()->back();
    }
}
