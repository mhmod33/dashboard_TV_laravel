<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStore;
use App\Http\Requests\StoreSubadmin;
use App\Http\Requests\StoreSubadminCustomer;
use App\Http\Requests\UpdateCustomer;
use App\Http\Requests\UpdateSubadmin;
use App\Http\Requests\UpdateSubadminCustomer;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\SubadminResource;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Period;
use App\Models\Subadmin;
use Illuminate\Http\Request;

class SubadminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allSubAdmins = Subadmin::all();
        $subAdmins = SubadminResource::collection($allSubAdmins);
        return response()->json(['message' => 'returned all subadmins', 'subadmins' => $subAdmins]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addSubadmin(StoreSubadmin $request)
    {
        // Get the authenticated admin
        $admin = auth()->user();
        
        // Validate the request data
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);
        
        // Set the parent_admin_id to the current admin's ID
        $data['parent_admin_id'] = $admin->id;
        
        // Check if admin has enough balance
        $subadminBalance = $data['balance'];
        if ($admin->balance < $subadminBalance) {
            return response()->json(['message' => 'Insufficient balance to add subadmin'], 400);
        }
        
        // Decrease admin's balance
        $admin->update([
            'balance' => $admin->balance - $subadminBalance
        ]);
        
        // Create the subadmin
        $data['role'] = 'subadmin'; // Ensure role is set to subadmin
        
        // Create the subadmin directly in the Admin model to bypass the global scope
        $subadmin = Admin::create($data);
        
        return response()->json([
            'message' => 'Subadmin added successfully and balance decreased', 
            'subadmin' => $subadmin,
            'admin' => $admin
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subadmin = Subadmin::find($id);
        if (!$subadmin) {
            return response()->json(['message' => 'this subadmin is not found']);
        }
        return response()->json(['messsage' => 'returned successfully subadmin', 'subadmin' => $subadmin]);

    }

    public function subadminProfile()
    {
        $subadmin = auth()->user();
        return response()->json(['message' => 'returned my profile', 'subadmin' => $subadmin]);
    }

    public function getMyCustomers()
    {
        $subadmin = auth()->user();
        $allCustomers = Customer::where('admin_id', $subadmin->id)->get();
        $customers=CustomerResource::collection($allCustomers);
        return response()->json(['message' => 'reutrned customers', 'customers' => $customers], 200);
    }

    public function addMyCustomer(StoreSubadminCustomer $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        
        // Get the period price
        $period = Period::find($data['plan_id']);
        if (!$period) {
            return response()->json(['message' => 'Period not found'], 404);
        }
        
        // Check if user has enough balance
        if ($user->balance < $period->price) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }
        
        // Decrease user's balance
        $user->update([
            'balance' => $user->balance - $period->price
        ]);
        
        // Create the customer with the authenticated user's ID (admin or subadmin)
        $customer = Customer::create(array_merge($data, ['admin_id' => $user->id]));
        $newCustomer = new CustomerResource($customer);
        
        return response()->json([
            'message' => 'Customer created successfully and balance decreased', 
            'customer' => $newCustomer,
            'admin' => $user
        ], 201);
    }
    public function updateMyCustomer(UpdateSubadminCustomer $request, string $id)
    {
        $subadmin = auth()->user();
        $customer = Customer::find($id);
        $customer->update(array_merge($request->validated(), ['admin_id' => $subadmin->id]));

        return response()->json(['message' => 'updated successfully', 'customer' => $customer], 200);
    }
    public function deleteMyCustomer(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'this customer is not found'], 404);
        }
        $customer->delete();
        return response()->json(['message' => 'deleted successfully!']);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubadmin $request, string $id)
    {
        // Get the authenticated admin
        $admin = auth()->user();
        
        // Find the subadmin
        $subadmin = Subadmin::find($id);
        if (!$subadmin) {
            return response()->json(['message' => 'This subadmin is not found'], 404);
        }
        
        // Check if this subadmin belongs to the current admin
        if ($subadmin->parent_admin_id != $admin->id) {
            return response()->json(['message' => 'You are not authorized to update this subadmin'], 403);
        }
        
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);
        $data['role'] = 'subadmin'; // Ensure role remains subadmin
        
        // Calculate balance difference
        $currentBalance = $subadmin->balance;
        $newBalance = $data['balance'];
        $balanceDifference = $newBalance - $currentBalance;
        
        // If balance is increased, check if admin has enough balance
        if ($balanceDifference > 0) {
            if ($admin->balance < $balanceDifference) {
                return response()->json(['message' => 'Insufficient balance to increase subadmin balance'], 400);
            }
            
            // Decrease admin's balance by the difference
            $admin->update([
                'balance' => $admin->balance - $balanceDifference
            ]);
        } 
        // If balance is decreased, increase admin's balance
        else if ($balanceDifference < 0) {
            $admin->update([
                'balance' => $admin->balance + abs($balanceDifference)
            ]);
        }
        
        // Update the subadmin
        $subadmin->update($data);
        
        return response()->json([
            'message' => 'Subadmin updated successfully and balance adjusted', 
            'subadmin' => $subadmin,
            'admin' => $admin
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subadmin = Subadmin::find($id);
        if (!$subadmin) {
            return response()->json(['message' => 'this admin is not found']);
        }
        $subadmin->delete();
        return response()->json(['messsage' => 'deleted  subadmin successfully ']);
    }
}
