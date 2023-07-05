<?php
namespace App\Http\Controllers;
use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class StacksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('test');
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
        $validated_data = $request->validate([
            'name' => 'required|min:1|max:255'
        ]);
        $stack = Stack::create([
            'name' => $validated_data["name"],
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Stack created successfully',
            'data' => $stack  
        ]);



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stack = Stack::with('notecards')->findOrFail($id);

        return response()->json([
            "data" => $stack
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
        $stack = Stack::with('notecards')->findOrFail($id);
        if ($stack["user_id"] != Auth::id()) {
            return response()->json([
            'message' => 'UNAUTHORIZED',    
            ], 500);
        }
        $this->validate($request, [
            "name" => ['string', 'min:1', 'max:255']
        ]);
        $fillable_attributes = ['name'];
        foreach ($fillable_attributes as $attribute) {
            if ($request->has($attribute)) {
                $stack->{$attribute} = $request->input($attribute);
            }
        }
        $stack->save();
        return response()-> json([
            'message' => 'Stack updated successfully',
            'data' => $stack
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stack = Stack::with('notecards')->findOrFail($id);
        if ($stack["user_id"] != Auth::id()) {
            return response()->json([
            'message' => 'UNAUTHORIZED',    
            ], 500);
        }
        $stack->delete();
        return response()->json([
            'message' => "Stack deleted successfully",
            "data" => $stack,
        ]);
        //
    }
}
