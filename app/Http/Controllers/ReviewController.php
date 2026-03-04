<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\PaketTour;

class ReviewController extends Controller
{
    // ================= LIST =================
    public function index()
    {
        $query = Review::with(['user', 'vendor']);

        // Jika user adalah Vendor, hanya tampilkan review untuk vendor mereka
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
        }

        $reviews = $query->latest()->paginate(10);

        return view('backend.reviews.index', compact('reviews'));
    }

    // ================= STORE (USER SUBMIT) =================
    public function store(Request $request)
{
    $request->validate([
        'paket_id' => 'required|exists:paket_tours,id',
        'name'     => 'required|string|max:255',
        'rating'   => 'required|integer|min:1|max:5',
        'comment'  => 'required|string|max:1000',
        'photo_file' => 'nullable|image|max:2048',
    ]);

    $paket = PaketTour::findOrFail($request->paket_id);
    
    $photoPath = null;

    if ($request->hasFile('photo_file')) {
        $photoPath = $request->file('photo_file')->store('reviews', 'public');
    }

    Review::create([
        'paket_id'  => $request->paket_id,
        'vendor_id' => $paket->vendor_id ?? null,
        'user_id'   => auth()->id(), 
        'name'      => $request->name,
        'rating'    => $request->rating,
        'comment'   => $request->comment,
        'photo'     => $photoPath,
        'status'    => 'pending',
    ]);

    return back()->with('success', 'Ulasan berhasil dikirim');
}

    // ================= APPROVE =================
    public function approve($id)
    {
        $review = Review::findOrFail($id);

        if ($review->status !== 'pending') {
            return back()->with('error', 'Review sudah diproses');
        }

        $review->update([
            'status' => 'approved'
        ]);

        return back()->with('success', 'Review berhasil di-approve');
    }

    // ================= REJECT =================
    public function reject($id)
    {
        $review = Review::findOrFail($id);

        if ($review->status !== 'pending') {
            return back()->with('error', 'Review sudah diproses');
        }

        $review->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Review berhasil ditolak');
    }

    // ================= REPLY =================
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review = Review::findOrFail($id);

        // hanya boleh reply kalau sudah approved
        if ($review->status !== 'approved') {
            return back()->with('error', 'Review harus di-approve dulu');
        }

        $review->update([
            'admin_reply' => $request->admin_reply
        ]);

        return back()->with('success', 'Balasan berhasil dikirim');
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        $review->delete();

        return back()->with('success', 'Review berhasil dihapus');
    }
}