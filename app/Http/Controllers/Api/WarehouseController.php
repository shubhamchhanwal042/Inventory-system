<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;


class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Warehouse::paginate(10);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required',
            'location'=>'required'
        ]);

        $warehouse = Warehouse::create($data);

        return response()->json([
            'success' => true,
            'message' => 'warehouse added successfully!',
            'data' => $warehouse
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $warehouse = Warehouse::findOrFail($id);

    $warehouse->delete(); // soft delete

    return response()->json([
        'success' => true,
        'message' => 'Warehouse deleted successfully'
    ], 200);
}
}
