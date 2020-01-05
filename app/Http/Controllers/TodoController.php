<?php

namespace App\Http\Controllers;

use App\Todo;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\RouteNotFoundException;
use Exception;

use function GuzzleHttp\Promise\all;

class TodoController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth');
    // }

    public function index() {
        // logic to get all Todos goes here

        // if (Auth::check()) {
        //     Auth::user()->AauthAcessToken()->delete();
        // }
        try {
            $user = Auth::user();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "status" => "500",
                    "error" => $th
                ], 500);
        }
        // if (is_null($user)) {
        //     return response()->json(
        //         [
        //             "status" => "500",
        //             "error" => "User not logged"
        //         ], 500);
        // }
        $todo = Todo::where('user_id', $user->id)->get();
        if ($todo->count() > 0) {
            return response()->json([
                "status" => "200",
                "success" => $todo
            ], 200);
        } else {
            return response()->json(
                [
                    "status" => "500",
                    "error" => "Todos Not Found"
                ], 500);
        }
    }

    public function createTodo(Request $request) {
        // logic to create a Todo record goes here
        // if (!is_null($exception)) {
        //     return response()->json(
        //         [
        //             "status" => "500",
        //             "error" => $exception
        //         ], 500);
        // }
        $user = Auth::user();
        $validTodo = Validator::make($request->all(), [
            'title' => 'required|string|unique:todo|max:255',
            'description' => 'required|string|max:1024',
            'category' => 'required|string|max:255',
            'due' => 'required|date',
        ]);
        if ($validTodo->fails()) {
            return response()->json(
                [
                    "status" => "500",
                    "error" => $validTodo->errors()->all()
                ], 500);
        } else {
            try{
                $todo = new Todo;
                $todo->title = $request->title;
                $todo->description = $request->description;
                $todo->due = $request->due;
                $todo->category = $request->category;
                $todo->user_id = $user->id;
                $todo->save();
            } catch(\Exception $e){
                return response()->json(
                [
                    "status" => "500",
                    "error" => $e
                ], 500);
            }
            return response()->json(
                [
                    "status" => "200",
                    "success" => "Todo Created",
                    // "data" => Todo::where('user_id', $user->id)->get()
                ], 201);
        }
    }

    public function getTodo($id) {
        $user = Auth::user();
        $todo = Todo::where('id', $id)->where('user_id', $user->id)->first();
        if ($todo != null && $todo->exists()) {
            return response()->json([
                "status" => "200",
                "success" => "Todo Found",
                "data" => $todo->get()
            ], 201);
        } else {
            return response()->json([
                "status" => "404",
                "error" => "Could Not Found",
            ], 404);
        }
    }

    public function updateTodo(Request $request) {
        // logic to update a Todo record goes here
        $user = Auth::user();
        $checkTodoExists = Todo::where('id', $request->id)->exists();
        if ($checkTodoExists) {
            $validTodo = Validator::make($request->all(), [
                'title' => 'required|string|unique:todo|max:255',
                'description' => 'required|string|max:1024',
                'category' => 'required|string|max:255',
                'due' => 'required|date',
            ]);
            if ($validTodo->fails()) {
                return response()->json([
                    "status" => "404",
                    "error" => $validTodo->errors()->all()
                ], 404);
            }
            try {
                $data = $request->only('title', 'description', 'category', 'due');
                $todo = Todo::where('user_id', $user->id)->where('id', $request->id)->update($data);
                if ($todo) {
                    return response()->json([
                            "status" => "200",
                            "success" => "Todo Updated Successfully",
                            // "data" => Todo::where('user_id', $user->id)->get()
                        ], 201);
                } else {
                    return response()->json([
                        "status" => "404",
                        "error" => 'Could not update, Please try again'
                    ], 404);
                }
            } catch (\Throwable $er) {
                return response()->json([
                        "status" => "404",
                        "error" => $er
                    ], 404);
            }
        } else {
            return response()->json([
                    "status" => "404",
                    "error" => "Incorrect Data Provided",
                ], 404);
        }
    }

    public function deleteTodo ($id) {
        // logic to delete a Todo record goes here
        $user = Auth::user();
        $todo = Todo::where('user_id', $user->id)->where('id', $id)->first();
        if ($todo->exists()) {
            $todo->delete();

            return response()->json([
                    "status" => "200",
                    "success" => "Todo Deleted Successfully",
                    // "data" => Todo::where('user_id', $user->id)->get()
                ], 201);
        } else {
            return response()->json([
                "status" => "404",
                "error" => "Could Not Found",
            ], 404);
        }
    }
}

