<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ...

    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255', // Use 'first_name' instead of 'name'
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => Rule::in(Role::pluck('id')->toArray()), // Use 'role' instead of 'roles'
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'), // Use 'first_name' instead of 'name'
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->roles()->sync([$request->input('role')]); // Use 'role' instead of 'roles'

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255', // Use 'first_name' instead of 'name'
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => Rule::in(Role::pluck('id')->toArray()), // Use 'role' instead of 'roles'
        ]);

        $user->update([
            'first_name' => $request->input('first_name'), // Use 'first_name' instead of 'name'
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->filled('password')
                ? Hash::make($request->input('password'))
                : $user->password,
        ]);

        $user->roles()->sync([$request->input('role')]); // Use 'role' instead of 'roles'

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    
}
