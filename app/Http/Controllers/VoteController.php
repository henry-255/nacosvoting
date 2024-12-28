<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    // Show available candidates grouped by position
    public function index()
    {
        $candidates = Candidate::all()->groupBy('position'); // Or filter based on active election positions if necessary
        return view('vote.index', compact('candidates'));
    }

    // Store the vote submission
    public function store(Request $request)
{
    // Get the currently authenticated user
    $user = Auth::user();

    // Check if the user has already voted
    if ($user->has_voted) {
        return redirect()->route('dashboard')->with('error', 'You have already voted.');
    }

    // Validate the incoming request to ensure the votes are valid
    $request->validate([
        'votes' => 'required|array', // Ensure 'votes' is an array
        'votes.*' => 'exists:candidates,id', // Ensure each vote corresponds to a valid candidate ID
    ]);

    // Loop through the votes and store them
    foreach ($request->input('votes') as $position => $candidateId) {
        Vote::create([
            'user_id' => $user->id,
            'candidate_id' => $candidateId,
            'position' => $position,
        ]);
    }

    // Mark the user as having voted
    $user->update(['has_voted' => true]); // Ensure this works, or use manual save() method

    // Redirect back to the dashboard with a success message
    return redirect()->route('dashboard')->with('success', 'Thank you for voting!');
}

}
