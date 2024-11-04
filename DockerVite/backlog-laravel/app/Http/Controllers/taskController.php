<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Tag;

class taskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $tasks = Task::all();
        $tasks = Task::with('tags')->orderBy('id', 'desc')->get(); 
        // dd($tasks);
        return response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $task = Task::create([
            'name' => $request->name,
            'status' => $request->status
        ]);

        if ($request->has('tags')) {
            $task->tags()->sync($request->tags); // タグを中間テーブルに挿入
        }

        return response()->json($task->load('tags'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
