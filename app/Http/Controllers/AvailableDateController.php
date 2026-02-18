<?php

namespace App\Http\Controllers;

use App\Models\AvailableDate;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class AvailableDateController extends Controller
{
    public function index()
    {
        $dates = AvailableDate::with('tourPackage')->latest()->get();
        return view('backend.available_dates.index', compact('dates'));
    }

    public function create()
    {
        $packages = TourPackage::pluck('title','id');
        return view('backend.available_dates.form', compact('packages'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'date' => 'required|date',
            'quota' => 'required|integer|min:1'
        ]);

        // Pastikan booked tidak bisa diinput dari request
        unset($data['booked']);

        AvailableDate::create($data);

        return redirect()->route('available-dates.index')
            ->with('success','Tanggal tersedia ditambahkan');
    }

    public function edit(AvailableDate $availableDate)
    {
        $packages = TourPackage::pluck('title','id');

        return view('backend.backend.available_dates.form', [
            'dateItem' => $availableDate,
            'packages' => $packages
        ]);
    }

    public function update(Request $request, AvailableDate $availableDate)
    {

        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'date' => 'required|date',
            'quota' => 'required|integer|min:1'
        ]);

        // Pastikan booked tidak bisa diinput dari request
        unset($data['booked']);

        $availableDate->update($data);

        return redirect()->route('available_dates.index')
            ->with('success','Tanggal diperbarui');
    }

    public function destroy(AvailableDate $availableDate)
    {
        $availableDate->delete();
        return back()->with('success','Tanggal dihapus');
    }
}
