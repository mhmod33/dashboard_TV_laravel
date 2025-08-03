<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayment;
use App\Http\Requests\UpdatePayment;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allPayments = Payment::all();
        $payments = PaymentResource::collection($allPayments);
        return response()->json(['message' => 'returned all payments', 'payemnts' => $payments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayment $request)
    {
        $payment = Payment::create($request->validated());
        return response()->json(['message' => 'created successfully payemnt', 'payemnt' => $payment], 201);
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
    public function update(UpdatePayment $request, string $id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'this payment is not found']);
        }
        $payment->update($request->validated());
        return response()->json(['message' => 'updated successfully payemnt', 'payemnt' => $payment], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'this payment is not found']);
        }
        $payment->delete();
        return response()->json(['message' => 'deleted successfully!']);
    }
}
