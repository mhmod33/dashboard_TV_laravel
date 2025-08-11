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
        // Get the authenticated user
        $currentUser = auth()->user();
        $data = $request->validated();
        
        // Get the period price
        $period = Period::find($data['plan_id']);
        if (!$period) {
            return response()->json(['message' => 'Period not found'], 404);
        }
        
        // If superadmin is adding a customer for an admin
        if ($currentUser->role === 'superadmin' && isset($data['admin_id'])) {
            $admin = Admin::find($data['admin_id']);
            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }
            
            // Decrease admin's balance
            if ($admin->balance < $period->price) {
                return response()->json(['message' => 'Admin has insufficient balance'], 400);
            }
            
            $admin->update([
                'balance' => $admin->balance - $period->price
            ]);
            
            // Create the customer
            $customer = Customer::create($data);
            
            return response()->json([
                'message' => 'Customer created successfully and admin balance decreased', 
                'customer' => $customer,
                'admin' => $admin
            ]);
        }
        // If admin is adding a customer for themselves
        else if ($currentUser->role === 'admin') {
            // Check if admin has enough balance
            if ($currentUser->balance < $period->price) {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }
            
            // Decrease admin's balance
            $currentUser->update([
                'balance' => $currentUser->balance - $period->price
            ]);
            
            // If admin_id is not set, set it to the current admin's id
            if (!isset($data['admin_id'])) {
                $data['admin_id'] = $currentUser->id;
            }
            
            // Create the customer
            $customer = Customer::create($data);
            
            return response()->json([
                'message' => 'Customer created successfully and balance decreased', 
                'customer' => $customer,
                'admin' => $currentUser
            ]);
        }
        
        // Default case (should not happen with proper role checks)
        $customer = Customer::create($data);
        return response()->json(['message' => 'created successfully', 'customer' => $customer]);
    }

    public function update(UpdateCustomer $request, string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Get the authenticated user
        $currentUser = auth()->user();
        $data = $request->validated();
        $data['plan_id'] = (int)$data['plan_id'];
        
        // Check if plan ID has changed
        if ($customer->plan_id != $data['plan_id']) {
            // Get the old and new period prices
            $oldPeriod = Period::find($customer->plan_id);
            $newPeriod = Period::find($data['plan_id']);
            
            if (!$oldPeriod || !$newPeriod) {
                return response()->json(['message' => 'Period not found'], 404);
            }
            
            // Calculate price difference
            $priceDifference = $newPeriod->price - $oldPeriod->price;
        } else {
            // No plan change, no price difference
            $priceDifference = 0;
        }
        
        // If price is increasing, check and update balance
        if ($priceDifference > 0) {
            // If superadmin is updating a customer for an admin
            if ($currentUser->role === 'superadmin' && isset($data['admin_id'])) {
                $admin = Admin::find($data['admin_id']);
                if (!$admin) {
                    return response()->json(['message' => 'Admin not found'], 404);
                }
                
                // Check if admin has enough balance
                if ($admin->balance < $priceDifference) {
                    return response()->json(['message' => 'Admin has insufficient balance'], 400);
                }
                
                // Decrease admin's balance
                $admin->update([
                    'balance' => $admin->balance - $priceDifference
                ]);
                
                // Update the customer
                $customer->update($data);
                
                return response()->json([
                    'message' => 'Customer updated successfully and admin balance adjusted', 
                    'customer' => new CustomerResource($customer),
                    'admin' => $admin,
                    'price_difference' => $priceDifference
                ]);
            }
            // If admin is updating their own customer
            else if ($currentUser->role === 'admin') {
                // Check if admin has enough balance
                if ($currentUser->balance < $priceDifference) {
                    return response()->json(['message' => 'Insufficient balance'], 400);
                }
                
                // Decrease admin's balance
                $currentUser->update([
                    'balance' => $currentUser->balance - $priceDifference
                ]);
                
                // Update the customer
                $customer->update($data);
                
                return response()->json([
                    'message' => 'Customer updated successfully and balance adjusted', 
                    'customer' => new CustomerResource($customer),
                    'admin' => $currentUser,
                    'price_difference' => $priceDifference
                ]);
            }
            // If subadmin is updating their customer
            else if ($currentUser->role === 'subadmin') {
                // Get the parent admin
                $admin = Admin::find($currentUser->parent_admin_id);
                if (!$admin) {
                    return response()->json(['message' => 'Parent admin not found'], 404);
                }
                
                // Check if subadmin has enough balance
                if ($currentUser->balance < $priceDifference) {
                    return response()->json(['message' => 'Insufficient balance'], 400);
                }
                
                // Decrease subadmin's balance
                $currentUser->update([
                    'balance' => $currentUser->balance - $priceDifference
                ]);
                
                // Update the customer
                $customer->update($data);
                
                return response()->json([
                    'message' => 'Customer updated successfully and balance adjusted', 
                    'customer' => new CustomerResource($customer),
                    'subadmin' => $currentUser,
                    'price_difference' => $priceDifference
                ]);
            }
        }
        // If price is decreasing, refund the difference
        else if ($priceDifference < 0) {
            $refundAmount = abs($priceDifference);
            
            // If superadmin is updating a customer for an admin
            if ($currentUser->role === 'superadmin' && isset($data['admin_id'])) {
                $admin = Admin::find($data['admin_id']);
                if (!$admin) {
                    return response()->json(['message' => 'Admin not found'], 404);
                }
                
                // Increase admin's balance
                $admin->update([
                    'balance' => $admin->balance + $refundAmount
                ]);
                
                // Update the customer
                $customer->update($data);
                
                return response()->json([
                    'message' => 'Customer updated successfully and admin balance refunded', 
                    'customer' => new CustomerResource($customer),
                    'admin' => $admin,
                    'price_difference' => $priceDifference
                ]);
            }
            // If admin is updating their own customer
            else if ($currentUser->role === 'admin') {
                // Increase admin's balance
                $currentUser->update([
                    'balance' => $currentUser->balance + $refundAmount
                ]);
                
                // Update the customer
                $customer->update($data);
                
                return response()->json([
                    'message' => 'Customer updated successfully and balance refunded', 
                    'customer' => new CustomerResource($customer),
                    'admin' => $currentUser,
                    'price_difference' => $priceDifference
                ]);
            }
            // If subadmin is updating their customer
            else if ($currentUser->role === 'subadmin') {
                // Increase subadmin's balance
                $currentUser->update([
                    'balance' => $currentUser->balance + $refundAmount
                ]);
                
                // Update the customer
                $customer->update($data);
                
                return response()->json([
                    'message' => 'Customer updated successfully and balance refunded', 
                    'customer' => new CustomerResource($customer),
                    'subadmin' => $currentUser,
                    'price_difference' => $priceDifference
                ]);
            }
        }
        
        // If no price change or default case
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

    Customer::whereIn('id', $request->customer_ids)
        ->update(['admin_id' => $request->admin_id]);

    return response()->json(['message' => 'Admin updated successfully.']);
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
