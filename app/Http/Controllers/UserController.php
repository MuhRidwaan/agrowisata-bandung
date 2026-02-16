<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;



class UserController extends Controller
{
   public function index(Request $request)
{
    $query = User::query();

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    $users = $query
        ->latest()
        ->paginate(10) // jumlah per halaman
        ->withQueryString(); // supaya search tidak hilang

    return view('backend.users.index', compact('users'));
}

    

   
public function create()
{
    // pakai 1 form
    $roles = Role::all();
    return view('backend.users.form', compact('roles'));
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

    $user->assignRole($request->role);

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
    $roles = Role::all();
    return view('backend.users.form', compact('user', 'roles'));
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
    $user->syncRoles($request->role);
    

    return redirect()
        ->route('users.index')
        ->with('success','User berhasil diupdate');
}

public function destroy(User $user)
{
    $user->delete();

    return redirect()
        ->route('users.index')
        ->with('success', 'User berhasil dihapus');
}


public function export()
{
    return Excel::download(new UsersExport, 'users.xlsx');
}
}
