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
        // $tasks = Task::with('tags')->orderBy('id', 'desc')->get();
        $tasks = Task::with('tags')->orderBy('order', 'asc')->get();
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
            'order' => 1
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
    public function update(Request $request, int $id)
    {
        $task = Task::findOrfail($id);
        $oldStatus = $task->status;
        $newStatus = $request->status;
        $task->status = $newStatus;
        $oldOrder = $task->order;
        $newOrder = $request->order;
        $task->order = $newOrder;

        if ($oldStatus == $newStatus) {
            if ($newOrder < $oldOrder) {
                Task::where('status', $oldStatus)
                    ->where('order', '<', $oldOrder)
                    ->where('order', '>=', $newOrder)
                    ->whereNotNull('order')
                    ->increment('order');
            } elseif ($newOrder > $oldOrder) {
                Task::where('status', $oldStatus)
                    ->where('order', '>', $oldOrder)
                    ->where('order', '<=', $newOrder)
                    ->whereNotNull('order')
                    ->decrement('order');
            }
        } else {
            Task::where('status', $oldStatus)
                ->where('order', '>', $oldOrder)
                ->decrement('order');
            Task::where('status', $newStatus)
                ->where('order', '>=', $newOrder)
                ->increment('order');
        }
        $tasks = Task::orderBy('order')->get();
        $task->save();
        return response()->json($tasks);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Find the task to delete
        $task = Task::findOrFail($id);
        $deletedTaskStatus = $task->status;
        $deletedTaskOrder = $task->order;
        // Delete the task
        $task->delete();

        // Update order for tasks with a higher order than the deleted task
        Task::where('status', $deletedTaskStatus)
            ->where('order', '>', $deletedTaskOrder)
            ->whereNotNull('order')
            ->decrement('order');

        return response()->json($task);
    }
}
