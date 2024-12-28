<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $candidates = Candidate::all();
        return view('admin.dashboard', compact('candidates'));
    }

    public function storeCandidate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string',
            'gender' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = $request->file('photo')->store('candidates', 'public');

        Candidate::create([
            'name' => $validated['name'],
            'position' => $validated['position'],
            'gender' => $validated['gender'],
            'photo_url' => $photoPath,
        ]);

        return back()->with('success', 'Candidate added successfully.');
    }
}

