<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // LIST
    public function index()
    {
        $reviews = Review::with('vendor')->get();
        return view('reviews.index', compact('reviews'));
    }

    // FORM CREATE
    public function create()
    {
        $vendors = Vendor::all();
        return view('reviews.form', compact('vendors'));
    }

    // STORE
    public function store(Request $request)
    {
        Review::create([
            'user_id' => 1,
            'vendor_id' => $request->vendor_id,
            'name' => $request->name,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('review.index');
    }

    // FORM EDIT
    public function edit($id)
    {
        $review = Review::findOrFail($id);
        $vendors = Vendor::all();

        return view('reviews.form', compact('review','vendors'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $review->update([
            'vendor_id' => $request->vendor_id,
            'name' => $request->name,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('review.index');
    }

    // DELETE
    public function destroy($id)
    {
        Review::destroy($id);
        return redirect()->route('review.index');
    }
}
