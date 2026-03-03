<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Vendor;

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
    $vendors = Vendor::whereNull('user_id')->get();
    return view('backend.users.form', compact('roles', 'vendors'));
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
        'vendor_id' => 'nullable|exists:vendors,id',
    ]);

    $user=User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole($request->role);

    // Jika role-nya Vendor dan ada vendor_id terpilih, link ke vendor tersebut
    if ($request->role === 'Vendor' && $request->vendor_id) {
        $vendor = Vendor::find($request->vendor_id);
        if ($vendor) {
            $vendor->update(['user_id' => $user->id]);
        }
    }

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
    // Ambil vendor yang belum punya user, ATAU vendor yang sudah terikat dengan user ini
    $vendors = Vendor::where(function($q) use ($user) {
        $q->whereNull('user_id')
          ->orWhere('user_id', $user->id);
    })->get();

    return view('backend.users.form', compact('user', 'roles', 'vendors'));
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
        'vendor_id' => 'nullable|exists:vendors,id',
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

    // Update relasi Vendor
    if ($request->role === 'Vendor') {
        // Hapus link dari vendor lama (jika ada)
        Vendor::where('user_id', $user->id)->update(['user_id' => null]);
        
        // Link ke vendor baru
        if ($request->vendor_id) {
            $vendor = Vendor::find($request->vendor_id);
            if ($vendor) {
                $vendor->update(['user_id' => $user->id]);
            }
        }
    } else {
        // Jika role diubah dari Vendor ke lainnya, hapus link vendor
        Vendor::where('user_id', $user->id)->update(['user_id' => null]);
    }
    

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
