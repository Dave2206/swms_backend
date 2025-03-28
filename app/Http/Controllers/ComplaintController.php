<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'subject' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'nullable|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Unique filename
            $image->move(public_path('attachments'), $imageName); // Move file to `public/announcement/`
        } else {
            $imageName = null;
        }

        // Store complaint in the database
        $complaint = Complaint::create([
            'subject' => $validatedData['subject'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'] ?? '',
            'description' => $validatedData['description'],
            'file_path' => $imageName,
        ]);

        return response()->json(['message' => 'Complaint submitted successfully!', 'complaint' => $complaint], 201);
    }

    /**
     * Fetch all complaints where is_verified is null (for verification).
     */
    public function verificationIndex()
    {
        $complaints = Complaint::whereNull('is_verified')
            ->OrWhere('is_verified','=','0')
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    /**
     * Fetch all complaints where is_verified is 1 and is_assigned is null (for assignment).
     */
    public function assignmentIndex()
    {
        $complaints = Complaint::where('is_verified', 1)
                ->whereNull('is_assigned')
                ->latest()
                ->get();
                    
        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    /**
     * Fetch all complaints.
     */
    public function index()
    {
        $complains = Complaint::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $complains
        ]);
    }

    /**
     * Update concern verification status.
     */
    public function update(Request $request, $id)
    {
        $concern = Complaint::findOrFail($id);
        $request->validate([
            'is_verified' => 'required|integer|in:1,9',
        ]);
        
        $concern->is_verified = $request->input('is_verified');
        $concern->save();
        
        return response()->json(['message' => 'Concern updated successfully.', 'concern' => $concern]);
    }

    public function assignToDriver($id, Request $request)
    {
        // Validate request
        $request->validate([
            'is_assigned' => 'required|boolean',
            'assigned_to' => 'required|exists:users,id',  // Ensure that the driver exists
        ]);

        // Find the complaint
        $complaint = Complaint::find($id);

        if (!$complaint) {
            return response()->json([
                'message' => 'Complaint not found.',
            ], 404);
        }

        // Update assignment details
        $complaint->is_assigned = $request->input('is_assigned');
        $complaint->assigned_to = $request->input('assigned_to');
        $complaint->save();


        // Return response
        return response()->json([
            'message' => 'Concern assigned successfully.',
            'complaint' => $complaint,
        ]);
    }

    public function assignedRoutes(Request $request)
    {
        $userId = auth()->user()->id; // Get the authenticated user's ID
        
        $assignedRoutes = Complaint::where('assigned_to', $userId)
                                    ->whereNull('is_resolved')
                                    ->get();

        return response()->json($assignedRoutes);
    }
    public function resolve(Request $request)
    {
        $request->validate([
            'routeId' => 'required|exists:complaints,id',
        ]);

        $complaint = Complaint::findOrFail($request->routeId);
        $complaint->update(['is_resolved' => true]); // Mark as resolved

        return response()->json(['message' => 'Complaint resolved successfully']);
    }
}
