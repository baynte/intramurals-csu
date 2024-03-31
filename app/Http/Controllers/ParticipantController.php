<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Participant::where('year', '=', $request->year)
            ->orderBy('name', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|string|min:4',
            'name' => 'required|string|min:3',
            'description' => 'required|string|min:3',
        ]);
        Participant::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Participant $Participant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $c = Participant::findOrFail($id);
        $c->name = $request->name;
        $c->description = $request->description;
        $c->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $c = Participant::findOrFail($id);
        $c->delete();
        return response()->json(['msg' => 'removed']);
    }


    public function AvatarUpdate(Request $request){
        $Participant = Participant::findOrFail($request->id);
        
        $file = $request->file('file');
        // $name = $file->hashName();
        // $extension = $file->extension();
        $path = $file->store('avatars', ['disk' => 'public']);

        $Participant->avatar_path = $path;
        $Participant->save();

        return response()->json(['msg' => 'Success']);
    }

    public function BgUpdate(Request $request){
        $Participant = Participant::findOrFail($request->id);
        
        $file = $request->file('file');
        // $name = $file->hashName();
        // $extension = $file->extension();
        $path = $file->store('bg', ['disk' => 'public']);

        $Participant->bg_path = $path;
        $Participant->save();

        return response()->json(['msg' => 'Success']);
    }

    public function getItems(Request $request){
        return Participant::where('year', '=', $request->year)
        ->orderBy('name', 'ASC')
        ->get();
    }
}
