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

    // Get the authenticated user
    $currentUser = auth()->user();
    
    // If superadmin is increasing admin's balance, decrease superadmin's balance
    if ($currentUser->role === 'superadmin' && $admin->role === 'admin') {
        // Check if superadmin has enough balance
        if ($currentUser->balance < $request->balance) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }
        
        // Decrease superadmin's balance
        $currentUser->update([
            'balance' => $currentUser->balance - $request->balance
        ]);
        
        // Increase admin's balance
        $admin->update([
            'balance' => $admin->balance + $request->balance
        ]);
        
        return response()->json([
            'message' => 'Balance updated successfully',
            'admin' => $admin,
            'superadmin' => $currentUser
        ]);
    }
    
    // If superadmin is increasing subadmin's balance, it should affect the parent admin's balance
    // and not affect the superadmin's balance
    if ($currentUser->role === 'superadmin' && $admin->role === 'subadmin') {
        // Get the parent admin
        $parentAdmin = Admin::find($admin->parent_admin_id);
        
        if (!$parentAdmin) {
            return response()->json(['message' => 'Parent admin not found'], 404);
        }
        
        // Check if parent admin has enough balance
        if ($parentAdmin->balance < $request->balance) {
            return response()->json(['message' => 'Insufficient balance in parent admin account'], 400);
        }
        
        // Decrease parent admin's balance
        $parentAdmin->update([
            'balance' => $parentAdmin->balance - $request->balance
        ]);
        
        // Increase subadmin's balance
        $admin->update([
            'balance' => $admin->balance + $request->balance
        ]);
        
        return response()->json([
            'message' => 'Balance updated successfully',
            'admin' => $admin,
            'parent_admin' => $parentAdmin
        ]);
    }
    
    // If admin is increasing subadmin's balance, decrease admin's balance
    if ($currentUser->role === 'admin' && $admin->role === 'subadmin') {
        // Check if admin has enough balance
        if ($currentUser->balance < $request->balance) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }
        
        // Check if the subadmin belongs to this admin
        if ($admin->parent_admin_id != $currentUser->id) {
            return response()->json(['message' => 'Unauthorized. This subadmin is not linked to you'], 403);
        }
        
        // Decrease admin's balance
        $currentUser->update([
            'balance' => $currentUser->balance - $request->balance
        ]);
        
        // Increase subadmin's balance
        $admin->update([
            'balance' => $admin->balance + $request->balance
        ]);
        
        return response()->json([
            'message' => 'Balance updated successfully',
            'admin' => $admin,
            'current_user' => $currentUser
        ]);
    }
    
    // Default case - just increase balance
    $admin->update([
        'balance' => $admin->balance + $request->balance
    ]);

    return response()->json(['message' => 'Balance updated successfully', 'admin' => $admin]);
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
    
    // Check if admin has enough balance to decrease
    if ($admin->balance < $request->balance) {
        return response()->json(['message' => 'Insufficient balance to decrease'], 400);
    }

    // Get the authenticated user
    $currentUser = auth()->user();
    
    // If superadmin is decreasing admin's balance, increase superadmin's balance
    if ($currentUser->role === 'superadmin' && $admin->role === 'admin') {
        // Decrease admin's balance
        $admin->update([
            'balance' => $admin->balance - $request->balance
        ]);
        
        // Increase superadmin's balance
        $currentUser->update([
            'balance' => $currentUser->balance + $request->balance
        ]);
        
        return response()->json([
            'message' => 'Balance decreased successfully by '.$request->balance,
            'admin' => $admin,
            'superadmin' => $currentUser
        ]);
    }
    
    // If superadmin is decreasing subadmin's balance, increase the parent admin's balance
    // and not affect the superadmin's balance
    if ($currentUser->role === 'superadmin' && $admin->role === 'subadmin') {
        // Get the parent admin
        $parentAdmin = Admin::find($admin->parent_admin_id);
        
        if (!$parentAdmin) {
            return response()->json(['message' => 'Parent admin not found'], 404);
        }
        
        // Decrease subadmin's balance
        $admin->update([
            'balance' => $admin->balance - $request->balance
        ]);
        
        // Increase parent admin's balance
        $parentAdmin->update([
            'balance' => $parentAdmin->balance + $request->balance
        ]);
        
        return response()->json([
            'message' => 'Balance decreased successfully by '.$request->balance,
            'admin' => $admin,
            'parent_admin' => $parentAdmin
        ]);
    }
    
    // If admin is decreasing subadmin's balance, increase admin's balance
    if ($currentUser->role === 'admin' && $admin->role === 'subadmin') {
        // Check if the subadmin belongs to this admin
        if ($admin->parent_admin_id != $currentUser->id) {
            return response()->json(['message' => 'Unauthorized. This subadmin is not linked to you'], 403);
        }
        
        // Decrease subadmin's balance
        $admin->update([
            'balance' => $admin->balance - $request->balance
        ]);
        
        // Increase admin's balance
        $currentUser->update([
            'balance' => $currentUser->balance + $request->balance
        ]);
        
        return response()->json([
            'message' => 'Balance decreased successfully by '.$request->balance,
            'admin' => $admin,
            'current_user' => $currentUser
        ]);
    }

    // Default case - just decrease balance
    $admin->update([
        'balance' => $admin->balance - $request->balance
    ]);

    return response()->json(['message' => 'Balance decreased successfully by '.$request->balance, 'admin' => $admin]);
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
