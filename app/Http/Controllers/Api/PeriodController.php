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
        return response()->json(['message' => 'returned all periods', 'periods' => $periods]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePeriod $request)
    {
        try {
            $period = Period::create($request->validated()); // No longer setting plan manually
            return response()->json([
                'message' => 'Period created successfully',
                'period' => new PeriodResource($period)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create period',
                'error' => $e->getMessage()
            ], 500);
        }
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
            return response()->json(['message' => 'Period not found'], 404);
        }

        try {
            $period->update($request->validated()); // No longer setting plan manually
            return response()->json([
                'message' => 'Period updated successfully',
                'period' => new PeriodResource($period)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update period',
                'error' => $e->getMessage()
            ], 500);
        }
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
