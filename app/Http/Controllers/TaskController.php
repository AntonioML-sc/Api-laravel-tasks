<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {

        try {
            Log::info('Creating task');

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255', 'min:3'],
                'status' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $validator->errors()
                    ],
                    400
                );
            }

            $user_id = auth()->user()->id;

            $title = $request->input("title");
            $status = $request->input("status");

            $task = new Task();

            $task->title = $title;
            $task->user_id = $user_id;
            $task->status = $status;

            $task->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'New task created'
                ],
                201
            );
        } catch (\Exception $exception) {

            Log::error("Error creating task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error creating task'
                ],
                500
            );
        }
    }

    public function myTasks()
    {

        try {

            Log::info("Getting all logged user's tasks");

            $user_id = auth()->user()->id;

            // $tasks = Task::query()->select()->where('user_id', "=", $user_id)->get()->toArray();

            // a través del modelo User:
            $tasks = User::query()->find($user_id)->tasks;

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Tasks retrieved successfully',
                    'data' => $tasks
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting tasks: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error creating task'
                ],
                500
            );
        }
    }

    public function deleteMyTask($id)
    {

        try {
            Log::info('Updating task');

            $task = Task::find($id);
            $user_id = auth()->user()->id;

            if (!$task) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Task not found'
                    ],
                    404
                );
            }

            if ($task->user_id != $user_id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Authentication failed'
                    ],
                    403
                );
            }

            $task->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Task deleted'
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error deleting task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error deleting task'
                ],
                500
            );
        }
    }

    public function getTaskById($id)
    {
        try {

            Log::info("Getting all logged user's tasks");

            $task = Task::find($id);
            $user_id = auth()->user()->id;

            if (!$task) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Task not found'
                    ],
                    404
                );
            }

            if ($task->user_id != $user_id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Authentication failed'
                    ],
                    403
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Tasks retrieved successfully',
                    'data' => $task
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting tasks: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error getting task'
                ],
                500
            );
        }
    }

    public function updateMyTask(Request $request, $id)
    {

        try {

            Log::info('Updating task');

            $task = Task::find($id);
            $user_id = auth()->user()->id;

            if (!$task) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Task not found'
                    ],
                    404
                );
            }

            if ($task->user_id != $user_id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Authentication failed'
                    ],
                    403
                );
            }

            $validator = Validator::make($request->all(), [
                'title' => ['string', 'max:255', 'min:3'],
                'status' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $validator->errors()
                    ],
                    400
                );
            }

            $title = $request->input("title");
            $status = $request->input("status");

            if (isset($title)) {
                $task->title = $title;
            }

            if (isset($status)) {
                $task->status = $status;
            }

            $task->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Task updated'
                ],
                201
            );
        } catch (\Exception $exception) {

            Log::error("Error updating task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error updating task'
                ],
                500
            );
        }
    }

    public function getUserByTaskId($id) {
        try {
            
            $user = Task::query()->find($id)->user;

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User found',
                    'data' => $user
                ],
                200
            );

        } catch (\Exception $exception) {
            
            Log::error("Error getting task by id: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error getting task by id'
                ],
                500
            );
        }
    }
}
