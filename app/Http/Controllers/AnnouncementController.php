<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    /**
     * Fetch all announcements.
     */
    public function index()
    {
        $announcements = Announcement::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $announcements,
        ]);
    }

    /**
     * Create a new announcement.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'subject' => 'required|string|max:255',
            'dateTime' => 'required|date',
            'route' => 'required|string',
            'context' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional image validation
        ]);

        // Handle image upload if exists
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('public/storage/announcement'); // Store image in 'public/announcement'
            // Get the filename from the storage path
            $imageFilename = basename($imagePath);
        } else {
            $imageFilename = null; // No image uploaded
        }

        $user = auth()->user()->name;

        // Create the announcement
        $announcement = Announcement::create([
            'subject' => $request->subject,
            'effective_date' => $request->dateTime, // Assuming dateTime is the effective date
            'image_path' => $imageFilename, // Store only the filename
            'body' => $request->context, // Assuming context is the body of the announcement
            'route' => $request->route,
            'author' => $user,
        ]);

        // Return response
        return response()->json(['message' => 'Announcement created successfully!', 'data' => $announcement], 201);
    }
}
