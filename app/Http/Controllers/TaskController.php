<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return view('tasks');
    }
    public function tasksList(Request $request)
    {
        if($request->type=="all"){
            $tasks = Task::all();
        }
        else{
            $tasks = Task::where('status', 0)->get();
        }
        return response()->json($tasks);
    }

    public function store(Request $request)
    {

        $existingTask = Task::where('name', $request->input('name'))->first();

        if ($existingTask) {
            return response()->json(['error' => 'Task with this name already exists.'],409);
        }

        $task = Task::create($request->all());
        return response()->json([ 'success' => 'Task Added successfully!' ]);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return response()->json([ 'success' => 'Task Completed successfully!' ]);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' =>'Task deleted successfully']);
    }
}
