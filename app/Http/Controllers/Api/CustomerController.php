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

    public function activeCustomers()
    {
        $active = Customer::where('status', 'active')->get();
        $activeCustomers = CustomerResource::collection($active);

        return response()->json(['active', $activeCustomers]);
    }
    public function expiredCustomers()
    {
        $expired = Customer::where('status', 'expired')->get();
        $expiredCustomers = CustomerResource::collection($expired);

        return response()->json(['expired', $expiredCustomers]);
    }
    public function paidCustomers()
    {
        $paid = Customer::where('payment_status', 'paid')->get();
        $paidCustomers = CustomerResource::collection($paid);
        return response()->json(['paid', $paidCustomers]);
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
            return response()->json(['message' => 'this customer is not found']);
        }
        $customer->update($request->validated());
        return response()->json(['message' => 'updated successfully', 'new customer' => $customer]);

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
    public function bulkChangeAdmin(Request $request)
    {
    $request->validate([
        'customer_ids' => 'required|array',
        'customer_ids.*' => 'exists:customers,id',
        'admin_id' => 'required|exists:admins,id',
    ]);

    Customer::whereIn('id', $request->customer_ids)
        ->update(['admin_id' => $request->admin_id]);

    return response()->json(['message' => 'Admin changed successfully.']);
    }

    public function bulkDeleteSelected(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        Customer::whereIn('id', $request->customer_ids)->delete();
        return response()->json(['message' => 'customers deleted successfully.']);

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
}
