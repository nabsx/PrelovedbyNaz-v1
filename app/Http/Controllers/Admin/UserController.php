<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $userTransactions = $user->transactions()->with('items.product')->latest()->get();
        
        return view('admin.users.show', compact('user', 'userTransactions'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }
}