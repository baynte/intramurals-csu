<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Post::where('year', '=', $request->year)
            ->orderBy('created_at', 'desc')->get();
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
        Post::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $c = Post::findOrFail($id);
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
        $c = Post::findOrFail($id);
        $c->delete();
        return response()->json(['msg' => 'removed']);
    }

    public function BgUpdate(Request $request){
        $post = Post::findOrFail($request->id);
        
        $file = $request->file('file');
        $path = $file->store('bg', ['disk' => 'public']);

        $post->bg_path = $path;
        $post->save();

        return response()->json(['msg' => 'Success']);
    }
}
