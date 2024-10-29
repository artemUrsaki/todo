<?php

namespace App\Http\Controllers;

use App\Filters\CategoryFilter;
use App\Http\Requests\CategoryTaskRequest;
use App\Http\Requests\ShareTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Mail\TaskMail;
use App\Models\Category;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        return TaskResource::collection(
            QueryBuilder::for(Task::class)
            ->leftJoin('task_categories', 'tasks.id', '=', 'task_categories.task_id')
            ->leftJoin('categories', 'task_categories.category_id', '=', 'categories.category_id')
            ->whereRaw('(user_id = ? or id in (select task_id from shared_tasks where shared_with_user_id = ?))', [$user->id, $user->id])
            ->allowedFilters([
                AllowedFilter::custom('category', new CategoryFilter),
                AllowedFilter::exact('is_completed'),
            ])
            ->paginate(5)
        );
    }  

    public function indexOwn(Request $request) {
        $user = $request->user();
        return TaskResource::collection(
            QueryBuilder::for(Task::class)
            ->leftJoin('task_categories', 'tasks.id', '=', 'task_categories.task_id')
            ->leftJoin('categories', 'task_categories.category_id', '=', 'categories.category_id')
            ->where('user_id', $user->id)
            ->allowedFilters([
                AllowedFilter::custom('category', new CategoryFilter),
                AllowedFilter::exact('is_completed'),
            ])
            ->paginate(5)
        );
    }   
    public function indexShared(Request $request) {
        $user = $request->user();
        return TaskResource::collection(
            QueryBuilder::for(Task::class)
            ->leftJoin('task_categories', 'tasks.id', '=', 'task_categories.task_id')
            ->leftJoin('categories', 'task_categories.category_id', '=', 'categories.category_id')
            ->whereIn('id', function (Builder $query) use ($user) {
                $query->select('task_id')
                ->from('shared_tasks')
                ->where('shared_with_user_id', $user->id);
            })
            ->allowedFilters([
                AllowedFilter::custom('category', new CategoryFilter),
                AllowedFilter::exact('is_completed'),
            ])
            ->paginate(5)
        );
    }   

    public function show(Task $task) {
        if (auth()->user()->cannot('view', $task)) {
            abort(403, 'You are not authorized');
        }
        return new TaskResource($task);
    }

    public function store(StoreTaskRequest $request) {
        $user = $request->user();
        $user_id = $user->id;
        $user_email = $user->email;
        
        Mail::to($user_email)->send(new TaskMail($user->name, "A new task has been created!"));

        return new TaskResource(Task::create([
            'name' => $request->name, 
            'content' => $request->content,
            'user_id' => $user_id
        ]));
    }

    public function update(UpdateTaskRequest $request, Task $task) {
        $user = $request->user();
        $user_email = $user->email;

        $task->update($request->all());

        Mail::to($user_email)->send(new TaskMail($user->name, "Your task has been updated!"));
    }

    public function destroy(Task $task) {
        if (auth()->user()->cannot('delete', $task)) {
            abort(403, 'You are not authorized');
        }
        $task->delete();

        $user = auth()->user();
        $user_email = $user->email;

        Mail::to($user_email)->send(new TaskMail($user->name, "Your task has been deleted!"));

    }

    public function restore($id) {
        $task = Task::withTrashed()->findOrFail($id);
        if (auth()->user()->cannot('restore', $task)) {
            abort(403, 'You are not authorized');
        }
        $task->restore();

        $user = auth()->user();
        $user_email = $user->email;

        Mail::to($user_email)->send(new TaskMail($user->name, "You restored your task!"));
    }

    public function share(ShareTaskRequest $request, Task $task) {
        $shared_with_user_id = $request->user_id;
        $user = auth()->user();
        $user_email = $user->email;

        $task->sharedTasks()->attach($shared_with_user_id);

        Mail::to($user_email)->send(new TaskMail($user->name, "You shared your task!"));
    }

    public function unShare(ShareTaskRequest $request, Task $task) {
        $shared_with_user_id = $request->user_id;
        $task->sharedTasks()->detach($shared_with_user_id);

        $user = auth()->user();
        $user_email = $user->email;

        Mail::to($user_email)->send(new TaskMail($user->name, "You unshared your task!"));
    }

    public function complete(Task $task) {
        $user = auth()->user();
        $user_email = $user->email;

        if ($user->cannot('complete', $task)) {
            abort(403, 'You are not authorized');
        }
        $is_completed = $task->is_completed;
        $task->update(['is_completed' => !$is_completed]);

        if ($is_completed) {
            Mail::to($user_email)->send(new TaskMail($user->name, "You task was marked as completed!"));
        } else {
            Mail::to($user_email)->send(new TaskMail($user->name, "You task was marked as uncompleted!"));
        }
    }

    public function setCategory(CategoryTaskRequest $request, Task $task) {
        $user = $request->user();
        $user_email = $user->email;

        $category_name = $request->category;
        $category_id = Category::select('id')->where('category', $category_name)->get();
        $task->categories()->attach($category_id);

        Mail::to($user_email)->send(new TaskMail($user->name, "You have assigned a category to your task!"));
    }
    
    public function unsetCategory(CategoryTaskRequest $request, Task $task) {
        $user = $request->user();
        $user_email = $user->email;
        
        $category_name = $request->category;
        $category_id = Category::select('id')->where('category', $category_name)->get();
        $task->categories()->detach($category_id);

        Mail::to($user_email)->send(new TaskMail($user->name, "You have removed the category of your task"));
    }
}
