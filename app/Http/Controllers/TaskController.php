<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryTaskRequest;
use App\Http\Requests\ShareTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use DB;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;

class TaskController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        return TaskResource::collection(
            Task::where('user_id', $user->id)
                ->orWhereIn(
                    'id',
                    DB::table('shared_tasks')
                        ->select('task_id')
                        ->where('shared_with_user_id', $user->id)
                )->orderBy('id')
                ->paginate(5)
        );
    }

    public function show(Task $task) {
        if (auth()->user()->cannot('view', $task)) {
            abort(403);
        }
        return new TaskResource($task);
    }

    public function store(StoreTaskRequest $request) {
        $user_id = $request->user()->id;
        return new TaskResource(Task::create([
            'name' => $request->name, 
            'content' => $request->content,
            'user_id' => $user_id
        ]));
    }

    public function update(UpdateTaskRequest $request, Task $task) {
        $task->update($request->all());
    }

    public function destroy(Task $task) {
        if (auth()->user()->cannot('delete', $task)) {
            abort(403, 'You are not authorized');
        }
        $task->delete();
    }

    public function restore($id) {
        $task = Task::withTrashed()->findOrFail($id);
        if (auth()->user()->cannot('restore', $task)) {
            abort(403, 'You are not authorized');
        }
        $task->restore();
    }

    public function share(ShareTaskRequest $request, Task $task) {
        $user_id = $request->user_id;

        $task->sharedTasks()->attach($user_id);
    }

    public function unShare(ShareTaskRequest $request, Task $task) {
        $user_id = $request->user_id;
        $task->sharedTasks()->detach($user_id);
    }

    public function complete(Task $task) {
        if (auth()->user()->cannot('complete', $task)) {
            abort(403, 'You are not authorized');
        }
        $is_completed = $task->is_completed;
        $task->update(['is_completed' => !$is_completed]);
    }

    public function setCategory(CategoryTaskRequest $request, Task $task) {
        $category_name = $request->category;
        $category_id = Category::select('id')->where('category', $category_name)->get();
        $task->categories()->attach($category_id);
    }

    public function unsetCategory(CategoryTaskRequest $request, Task $task) {
        $category_name = $request->category;
        $category_id = Category::select('id')->where('category', $category_name)->get();
        $task->categories()->detach($category_id);
    }
}
