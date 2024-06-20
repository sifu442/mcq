<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrollments = Enrollment::with(['course'])
            ->where('user_id', $user->id)
            ->get();

        return view('dashboard.index', compact('user', 'enrollments'));
    }

    

}
