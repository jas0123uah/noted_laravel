<?php

namespace App\Http\Controllers;
use App\Models\Notecard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotecardsController extends Controller
{
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
            'data' => $notecard  
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $notecard = Notecard::findOrFail($id);

        return response()->json([
            "data" => $notecard
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $notecard = Notecard::findOrFail($id);
        //The user may be studying the notecard or simply updating its contents.
        if ($request->has('quality')) {
            //The user is studying
            $request->validate([
                'quality' => 'required|integer|min:0|max:5'
            ]);
            $quality = $request->input('quality');
            // Update the repetition and interval values
            $notecard->repetition++;

            // Update the E-Factor based on the provided quality
            $notecard->updateEFactor($quality);

            // Restart repetitions or Calculate the next repetition
            $quality < 3 ? $notecard->restartRepetitions() : $notecard->calculateNextRepetition();

            
        } else {
            # code...
            $validated_data = $request->validate([
                'front' => 'required|min:1',
                'back' => 'required|min:1',
                'stack_id' => 'required|integer'
            ]);
            $fillable_attributes = ['front', 'back', 'stack_id'];
            foreach ($fillable_attributes as $attribute) {
                if ($request->has($attribute)) {
                    $notecard->{$attribute} = $request->input($attribute);
                }
            }
        }
        // Save the updated notecard
        $notecard -> save();
        return response()->json([
            "message" => "Notecard updated successfully",
            "data" => $notecard
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notecard = Notecard::findOrFail($id);
        $notecard->delete();
        return response()->json([
            "message" => "Notecard deleted successfully",
            "data" => $notecard
        ]);
    }
}
