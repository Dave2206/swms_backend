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
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('complaints', 'public');
        }

        // Store complaint in the database
        $complaint = Complaint::create([
            'subject' => $validatedData['subject'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'] ?? '',
            'description' => $validatedData['description'],
            'file_path' => $filePath,
        ]);

        return response()->json(['message' => 'Complaint submitted successfully!', 'complaint' => $complaint], 201);
    }
}
