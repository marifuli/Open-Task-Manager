<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct() {}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all users where is_admin is NOT true (i.e., false or null)
        $users = User::where('is_admin', '!=', true)->get();

        // Return the view with users data
        return view('users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return the view to create a new user
        return view('users.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'is_admin' => 'boolean',
        ]);

        $password = bcrypt('12345678'); // Set a default password or handle it as needed

        // Create a new user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'is_admin' => $request->is_admin ?? false, // Default to false if not provided
        ]); 

        // Redirect back with success message
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Show the user details
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Return the view to edit the user
        return view('users.edit', compact('user')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
        ]);
        // Update the user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ?? false, // Default to false if not provided
        ]);
        // Redirect back with success message
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();
        // Redirect back with success message
        return redirect()->route('users.index')->with('success', 'User deleted successfully.'); 
    }
}
