<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:admin,pharmacist,cashier,auditor',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'is_active' => true,
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_created',
            'description' => "Created new staff account for {$user->name} with role {$user->role}",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Mfanyakazi '{$user->name}' amesajiliwa kikamilifu.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:admin,pharmacist,cashier,auditor',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->role = $validated['role'];
        $user->is_active = $request->has('is_active');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_updated',
            'description' => "Updated staff account for {$user->name}",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Taarifa za mfanyakazi zimesasishwa.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Huwezi kufuta akaunti unayoitumia kwa sasa.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Hauwezi kufuta Msimamizi Mkuu wa pekee aliyebaki kwenye mfumo.');
        }

        $name = $user->name;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_deleted',
            'description' => "Deleted staff account for {$name}",
            'entity_type' => 'User',
            'entity_id' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Mfanyakazi '{$name}' amefutwa kikamilifu kwenye mfumo.");
    }
}
