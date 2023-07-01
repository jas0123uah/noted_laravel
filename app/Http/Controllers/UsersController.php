<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($id != Auth::id()) {
            return response()->json([
            'message' => 'USER_NOT_FOUND',    
            ], 404);
        }
        $user = User::where('user_id', $id)->get();
        return response()->json([
            'message' => 'testingstuff',
            'data' => $user
        ]);
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
        if ($id != Auth::id()) {
            return response()->json([
            'message' => 'UNAUTHORIZED',    
            ], 500);
        }

        $this->validate($request, [
            'first_name' => ['string', 'min:1', 'max:255'],
            'last_name' => ['string', 'min:1', 'max:255'],
            'email' => ['string', 'email', 'min:1', 'max:255', 'unique:users'],  
        ]);
        $model = User::findOrFail($id);

        $fillableAttributes = ['first_name', 'last_name', 'email'];

        foreach ($fillableAttributes as $attribute) {
            if ($request->has($attribute)) {
                $model->{$attribute} = $request->input($attribute);
            }
        }
        $model->save();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    if ($id != Auth::id()) {
            return response()->json([
            'message' => 'UNAUTHORIZED',    
            ], 500);
    }
    $user = User::where('user_id', $id)->firstOrFail();
    
    $user->delete();
    
    return response()->json([
        'message' => 'User deleted successfully',
        'user' => $user   
    ]);
}
}