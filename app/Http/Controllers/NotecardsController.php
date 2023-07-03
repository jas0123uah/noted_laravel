<?php

namespace App\Http\Controllers;
use App\Models\Notecard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class NotecardsController extends Controller
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
        $validated_data = $request->validate([
            'front' => 'required',
            'back' => 'required',
            'stack_id' => 'required'
        ]);
        
        $notecard = Notecard::create([
            'front' => $validated_data["front"],
            'back' => $validated_data["back"],
            'stack_id' => $validated_data["stack_id"],
            'user_id' => Auth::id(),
            'repetition' => 0,
        ]);

        return response()->json([
            'message' => 'Notecard created successfully',
            'notecard' => $notecard  
        ]);
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
        $validated_data = $request->validate([
            'front' => 'required',
            'back' => 'required',
            'stack_id' => 'required'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
