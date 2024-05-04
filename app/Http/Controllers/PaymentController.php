<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show($course_id)
{
    $course = Course::findOrFail($course_id);
    return view('payments.form', compact('course'));
}

    public function processPayment(Request $request)
    {
        // Validate and process the payment
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'payment_method' => 'required',
            'phone_number' => 'required',
        ]);

        // Store the payment request
    $payment = new Payment();
    $payment->user_id = auth()->id();
    $payment->course_id = $validated['course_id'];
    $payment->payment_method = $validated['payment_method'];
    $payment->phone_number = $validated['phone_number'];
    $payment->status = 'Pending'; // Default status
    $payment->save();

        return redirect('/');
    }
}
