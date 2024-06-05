<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show(Course $course)
    {
        return view('course.purchase', [
            'course' => $course,
            'user' => Auth::user(),
        ]);
    }

    public function purchase(Request $request, Course $course)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        // Handle the purchase logic here

        return redirect()->route('course.purchase', $course)->with('success', 'Purchase successful!');
    }
}
