<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
    
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
    
        $users = $query->latest()->get();
    
        return view('users.index', compact('users'));
    }
    

   
public function create()
{
    // pakai 1 form
    return view('users.form');
}


/* ======================
   STORE (CREATE)
====================== */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return redirect()
        ->route('users.index')
        ->with('success','User berhasil ditambahkan');
}


/* ======================
   EDIT
====================== */
public function edit(User $user)
{
    // kirim data user ke form yang sama
    return view('users.form', compact('user'));
}


/* ======================
   UPDATE
====================== */
public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
    ];

    // update password hanya kalau diisi
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()
        ->route('users.index')
        ->with('success','User berhasil diupdate');
}
}
