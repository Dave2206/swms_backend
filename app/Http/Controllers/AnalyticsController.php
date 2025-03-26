<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getAnalytics()
    {
        $totalConcerns = Complaint::count();
        $resolvedConcerns = Complaint::where('is_resolved', 1)->count();

        return response()->json([
            'total_concerns' => $totalConcerns,
            'resolved_concerns' => $resolvedConcerns,
        ]);
    }

    public function getComplaintReport()
    {
        // Run the query to group by subject and address
        $reportData = DB::table('complaints')
            ->select('subject', 'address', DB::raw('COUNT(*) as count'))
            ->groupBy('subject', 'address')
            ->get();

        return response()->json($reportData);
    }
}
