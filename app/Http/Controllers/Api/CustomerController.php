<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use App\Models\Subadmin;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerStore;
use App\Http\Requests\UpdateCustomer;
use App\Models\Period;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allCustomers = Customer::all();
        $customers = CustomerResource::collection($allCustomers);
        return response()->json(['message ' => 'returned successfully all customers', 'customers' => $customers]);
    }

    public function show(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json(['customer' => new CustomerResource($customer)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStore $request)
    {
        $customer = Customer::create($request->validated());
        return response()->json(['message' => 'created successfully', 'new customer', $customer]);
    }

    public function update(UpdateCustomer $request, string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Convert plan_id to integer if needed
        $data = $request->validated();
        $data['plan_id'] = (int)$data['plan_id'];

        $customer->update($data);

        return response()->json([
            'message' => 'Customer updated successfully',
            'customer' => new CustomerResource($customer)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'this customer is not found']);
        }
        $customer->delete();
        return response()->json(['message' => 'deleted successfully!']);
    }

    public function deleteAll()
    {
        if (auth()->user()->role != 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        Customer::turncate();
        return response()->json(['message' => 'All customers have been deleted.'], 200);
    }

    // In CustomerController.php
    public function getAllPeriods()
    {
        $periods = Period::all(); // Assuming you have a Period model
        return response()->json(['periods' => $periods]);
    }
}
