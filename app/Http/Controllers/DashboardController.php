<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

public function index()
{
    $user = Auth::user();
    $courses = Course::published()->get();

    return view('dashboard', compact('user', 'courses'));
}

}
