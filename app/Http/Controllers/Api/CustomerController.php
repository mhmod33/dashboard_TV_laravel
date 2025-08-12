<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStore;
use App\Http\Requests\UpdateCustomer;
use App\Http\Resources\CustomerResource;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Period;
use App\Models\Subadmin;
use Illuminate\Http\Request;

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
        $data = $request->validated();
        
        // Get the period price
        $period = Period::find($data['plan_id']);
        if (!$period) {
            return response()->json(['message' => 'Period not found'], 404);
        }
        
        // Get the parent admin (selected by superadmin)
        $admin = Admin::find($data['admin_id']);
        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }
        
        // Check if admin has enough balance
        if ($admin->balance < $period->price) {
            return response()->json(['message' => 'Insufficient balance for the selected admin'], 400);
        }
        
        // Decrease admin's balance
        $admin->update([
            'balance' => $admin->balance - $period->price
        ]);
        
        // Create the customer
        $customer = Customer::create($data);
        $newCustomer = new CustomerResource($customer);
        
        return response()->json([
            'message' => 'Customer created successfully and admin balance decreased', 
            'customer' => $newCustomer,
            'admin' => $admin
        ], 201);
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
        
        // Get the old period price
        $oldPeriod = Period::find($customer->plan_id);
        if (!$oldPeriod) {
            return response()->json(['message' => 'Current period not found'], 404);
        }
        
        // Get the new period price
        $newPeriod = Period::find($data['plan_id']);
        if (!$newPeriod) {
            return response()->json(['message' => 'New period not found'], 404);
        }
        
        // Check if admin_id is being changed
        $adminChanged = isset($data['admin_id']) && $data['admin_id'] != $customer->admin_id;
        $planChanged = $data['plan_id'] != $customer->plan_id;
        
        // If admin is changing, handle admin balance changes
        if ($adminChanged) {
            // Get the new admin
            $newAdmin = Admin::find($data['admin_id']);
            if (!$newAdmin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }
            
            // Check if new admin has enough balance
            if ($newAdmin->balance < $newPeriod->price) {
                return response()->json(['message' => 'Insufficient balance for the selected admin'], 400);
            }
            
            // Decrease new admin's balance
            $newAdmin->update([
                'balance' => $newAdmin->balance - $newPeriod->price
            ]);
            
            // If there was a previous admin, refund their balance
            if ($customer->admin_id) {
                $oldAdmin = Admin::find($customer->admin_id);
                if ($oldAdmin) {
                    $oldAdmin->update([
                        'balance' => $oldAdmin->balance + $oldPeriod->price
                    ]);
                }
            }
        }
        // If only plan is changing, update the current admin's balance
        else if ($planChanged && $customer->admin_id) {
            $admin = Admin::find($customer->admin_id);
            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }
            
            // Calculate price difference
            $priceDifference = $newPeriod->price - $oldPeriod->price;
            
            // If new plan is more expensive, check if admin has enough balance
            if ($priceDifference > 0 && $admin->balance < $priceDifference) {
                return response()->json(['message' => 'Insufficient balance for plan upgrade'], 400);
            }
            
            // Update admin's balance
            $admin->update([
                'balance' => $admin->balance - $priceDifference
            ]);
        }

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
        }
        Customer::turncate();
        return response()->json(['message' => 'All customers have been deleted.'], 200);
    }
public function bulkUpdatePaymentStatus(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'payment_status' => 'required| in:paid,unpaid'
        ]);

        Customer::whereIn('id', $request->customer_ids)->update(['payment_status' => $request->payment_status]);
        return response()->json(['message' => 'Payment status updated successfully.']);

    }
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'status' => 'required| in:active,expired'
        ]);

        Customer::whereIn('id', $request->customer_ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated successfully.']);

    }

    public function getCustomerySn(Request $request)
    {
        $sn = $request->serial_number;
        $customer = Customer::where('serial_number', $sn)->first();
        if (!$customer) {
            return response()->json(['message' => 'there is no customer with this sn'], 404);
        }
        $cusomerReturend = new CustomerResource($customer);
        return response()->json(['message' => 'returned successfully', 'customer' => $cusomerReturend]);
    }

    public function getCustomersByAdminId(string $id){
        $subadmin=Subadmin::find($id);
        $allCustomers=Customer::where('admin_id',$subadmin->id)->get();
        $customers=CustomerResource::collection($allCustomers);
        if(!$customers){
            return response()->json(['message'=>'this admin dont have any customers at this time'],404);
        }
        
        return response()->json(['message'=>'returned successfully customers','customers'=>$customers]);
    }
    public function bulkChangeAdmin(Request $request)
{
    $request->validate([
        'customer_ids' => 'required|array',
        'customer_ids.*' => 'exists:customers,id',
        'admin_id' => 'required|exists:admins,id',
    ]);
    
    // Get the new admin
    $newAdmin = Admin::find($request->admin_id);
    if (!$newAdmin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }
    
    // Get all affected customers
    $customers = Customer::whereIn('id', $request->customer_ids)->get();
    
    // Calculate total price for all customers
    $totalPrice = 0;
    foreach ($customers as $customer) {
        $period = Period::find($customer->plan_id);
        if ($period) {
            $totalPrice += $period->price;
        }
    }
    
    // Check if new admin has enough balance
    if ($newAdmin->balance < $totalPrice) {
        return response()->json(['message' => 'Insufficient balance for the selected admin'], 400);
    }
    
    // Process each customer
    foreach ($customers as $customer) {
        // Skip if admin is already the same
        if ($customer->admin_id == $request->admin_id) {
            continue;
        }
        
        $period = Period::find($customer->plan_id);
        if (!$period) {
            continue; // Skip if period not found
        }
        
        // Decrease new admin's balance
        $newAdmin->balance -= $period->price;
        
        // If there was a previous admin, refund their balance
        if ($customer->admin_id) {
            $oldAdmin = Admin::find($customer->admin_id);
            if ($oldAdmin) {
                $oldAdmin->update([
                    'balance' => $oldAdmin->balance + $period->price
                ]);
            }
        }
    }
    
    // Save the new admin's updated balance
    $newAdmin->save();
    
    // Update all customers at once
    Customer::whereIn('id', $request->customer_ids)
        ->update(['admin_id' => $request->admin_id]);

    return response()->json([
        'message' => 'Admin updated successfully and balance adjusted',
        'admin' => $newAdmin
    ]);
}

public function bulkDeleteSelected(Request $request)
{
    $request->validate([
        'customer_ids' => 'required|array',
        'customer_ids.*' => 'exists:customers,id',
    ]);

    Customer::whereIn('id', $request->customer_ids)->delete();

    return response()->json(['message' => 'Customers deleted successfully.']);
}
    // In CustomerController.php
    public function getAllPeriods()
    {
        $periods = Period::all(); // Assuming you have a Period model
        return response()->json(['periods' => $periods]);
    }
}
