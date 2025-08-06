<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStore;
use App\Http\Requests\AdminUpdate;
use App\Http\Resources\SubadminResource;
use App\Models\Admin;
use App\Http\Resources\AdminResource;
use App\Models\Subadmin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allAdmins = Admin::all();
        $admins = AdminResource::collection($allAdmins);
        return response()->json(['message' => 'returned all admins', 'admins' => $admins]);
    }

    // public function getAllAdmins(){
    //     $adminUsers
    // }
    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStore $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);
        $admin = Admin::create($data);
        return response()->json(['message' => 'created new admin successfully', 'admin' => $admin], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'this admin is not found']);
        }
        $adminData = new AdminResource($admin);
        return response()->json(['message' => 'returned successfully', 'admin' => $adminData]);
    }

    public function getMySubadmins()
    {
        $admin = auth()->user();
        $subadmins = Subadmin::where('parent_admin_id', $admin->id)->get();
        $mySubadmins = SubadminResource::collection($subadmins);
        return response()->json(['message' => 'returned successfully', 'subadmins' => $mySubadmins]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdate $request, string $id)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);
        $admin = Admin::find($id);
        $admin->update($data);
        return response()->json(['message' => 'admin updated successfully', 'admin' => $admin], 201);
    }

    public function increaseBalance(string $id, Request $request)
    {
    $request->validate([
        'balance' => 'required|numeric'
    ]);

    $admin = Admin::find($id);

    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }

    $admin->update([
        'balance' => $admin->balance + $request->balance
    ]);

    return response()->json(['message' => 'Balance updated successfully']);
    }
    public function decreaseBalance(string $id, Request $request)
    {
    $request->validate([
        'balance' => 'required|numeric'
    ]);

    $admin = Admin::find($id);

    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }

    $admin->update([
        'balance' => $admin->balance - $request->balance
    ]);

    return response()->json(['message' => 'Balance decreased successfully by '.$request->balance]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'this admin is not found']);
        }
        $admin->delete();
        return response()->json(['message' => 'deleted successfully!']);

    }
    public function ban(string $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'this admin is not found']);
        }
        $admin->update([
            'status'=>'inactive'
        ]);
        return response()->json(['message' => 'banned admin successfully!']);
    }


    public function getMyprofile()
    {
        $admin = auth()->user();
        return response()->json(['message' => 'returned my profile', 'admin' => $admin]);
    }
    public function getSuperadminProfile()
    {
        $superadmin = auth()->user();
        return response()->json(['message' => 'returned my profile', 'superadmin' => $superadmin]);
    }

}
