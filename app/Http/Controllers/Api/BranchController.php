<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Get all active branches
     */
    public function index()
    {
        $branches = Branch::active()->ordered()->get();
        
        return response()->json([
            'success' => true,
            'data' => $branches,
        ]);
    }

    /**
     * Get a specific branch
     */
    public function show($id)
    {
        $branch = Branch::active()->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $branch,
        ]);
    }
}
