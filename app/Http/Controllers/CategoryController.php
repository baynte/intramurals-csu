<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::orderBy('name', 'asc')->get();
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
        Category::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $c = Category::findOrFail($id);
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
        $c = Category::findOrFail($id);
        $c->delete();
        return response()->json(['msg' => 'removed']);
    }

    public function getItems(Request $request){
        return Category::where('year', '=', $request->year)
        ->orderBy('name', 'ASC')
        ->get();
    }
}
