<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    // ================= LIST =================
    public function index()
    {
        $reviews = Review::with(['user', 'vendor']) 
            ->latest()
            ->paginate(10);

        return view('backend.reviews.index', compact('reviews'));
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

        // 🔒 hanya boleh reply kalau sudah approved
        if ($review->status !== 'approved') {
            return back()->with('error', 'Review harus di-approve dulu');
        }

        $review->update([
            'admin_reply' => $request->admin_reply
        ]);

        return back()->with('success', 'Balasan berhasil dikirim');
    }
}