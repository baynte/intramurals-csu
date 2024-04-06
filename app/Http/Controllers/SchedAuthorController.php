<?php

namespace App\Http\Controllers;

use App\Models\SchedAuthor;
use Illuminate\Http\Request;

class SchedAuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // info($request);
        SchedAuthor::create([
            'enabled' => true,
            'sched_id' => $request->sched_id,
            'name' => strtolower($request->name),
            'position' => $request->position
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(SchedAuthor $schedAuthor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchedAuthor $schedAuthor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchedAuthor $schedAuthor)
    {
        //
    }
}
