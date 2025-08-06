<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStore;
use App\Http\Requests\StoreSubadmin;
use App\Http\Requests\StoreSubadminCustomer;
use App\Http\Requests\UpdateCustomer;
use App\Http\Requests\UpdateSubadmin;
use App\Http\Requests\UpdateSubadminCustomer;
use App\Http\Resources\SubadminResource;
use App\Models\Admin;
use App\Models\Customer;
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
    public function addSubadmin(StoreSubadmin $request, string $id)
    {
        $subadmin = Subadmin::find($id);
        if (!$subadmin) {
            return response()->json(['message' => 'this admin is not found']);
        }
        $subadmin->create($request->validated());
        return response()->json(['messsage' => 'subadmin successfully to admin with id' . $id, 'admin' => $subadmin]);
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

    public function subadminProfile(){
        $subadmin = auth()->user();
        return response()->json(['message'=>'returned my profile','subadmin'=>$subadmin]);
    }

    public function getMyCustomers()
    {
        $subadmin = auth()->user();
        $customers = Customer::where('admin_id', $subadmin->id)->get();

        return response()->json(['message' => 'reutrned customers', 'customers' => $customers], 200);
    }

    public function addMyCustomer(StoreSubadminCustomer $request)
    {
        $subadmin = auth()->user();
        $customer = Customer::create(array_merge($request->validated(), ['admin_id' => $subadmin->id]));

        return response()->json(['message' => 'created successfully', 'customer' => $customer], 201);
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
        $subadmin = Subadmin::find($id);
        if (!$subadmin) {
            return response()->json(['message' => 'this admin is not found']);
        }
        $subadmin->update($request->validated());
        return response()->json(['messsage' => 'updated subadmin successfully ', 'subadmin' => $subadmin]);
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
