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
        $announcements = Announcement::where('effective_date', '>=', now())
            ->latest()
            ->get();

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
    // Validate request
    $request->validate([
        'subject' => 'required|string|max:255',
        'dateTime' => 'required|date',
        'route' => 'required|string',
        'context' => 'required|string',
        'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Handle image upload
    if ($request->hasFile('attachment')) {
        $image = $request->file('attachment');
        $imageName = time() . '_' . $image->getClientOriginalName(); // Unique filename
        $image->move(public_path('announcement'), $imageName); // Move file to `public/announcement/`
    } else {
        $imageName = null;
    }

    $user = auth()->user()->name;

    // Save announcement in database
    $announcement = Announcement::create([
        'subject' => $request->subject,
        'effective_date' => $request->dateTime,
        'image_path' => $imageName, // Save only filename
        'body' => $request->context,
        'route' => $request->route,
        'author' => $user,
    ]);

    // Return response with full public URL
    return response()->json([
        'message' => 'Announcement created successfully!',
        'data' => $announcement,
        'image_url' => $imageName ? asset("announcement/$imageName") : null // Correct public URL
    ], 201);
}


}
