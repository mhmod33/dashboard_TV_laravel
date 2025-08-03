<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeriod;
use App\Http\Requests\UpdatePeriod;
use App\Http\Resources\PeriodResource;
use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allPeriods = Period::all();
        $periods = PeriodResource::collection($allPeriods);
        return response()->json(['message' => 'returned all periods', 'periods' => $periods]);    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeriod $request)
    {
        $period=Period::create($request->validated());
        return response()->json(['message' => 'created successfully period', 'period' => $period], 201);
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
    public function update(UpdatePeriod $request, string $id)
    {
        $period = Period::find($id);
        if (!$period) {
            return response()->json(['message' => 'this period is not found']);
        }
        $period->update($request->validated());
        return response()->json(['message' => 'updated successfully payemnt', 'payemnt' => $period], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $period = Period::find($id);
        if (!$period) {
            return response()->json(['message' => 'this period is not found']);
        }
        $period->delete();
        return response()->json(['message' => 'deleted successfully!']);
    }
}
