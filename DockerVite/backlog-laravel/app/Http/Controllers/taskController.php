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
        // 新しいタスクのステータスを取得
        $status = $request->status;

        // 同じステータスを持つタスクのorderを+1する
        Task::where('status', $status)
            ->whereNotNull('order') // orderがNULLでないタスクを対象
            ->increment('order');

        $task = Task::create([
            'name' => $request->name,
            'status' => $request->status,
            'order' => $request->order
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
        $task = Task::find($id);
        $task->order = $request->order;
        $task->save();
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
