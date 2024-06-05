@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-center text-2xl font-bold">Purchase Course</h1>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold">{{ $course->title }}</h2>
        <p>Fee: ${{ $course->fee }}</p>
        <p>User Name: {{ $user->name }}</p>
        <p>User ID: {{ $user->id }}</p>
        <form action="{{ route('course.purchase.submit', $course) }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                <input type="text" id="payment_method" name="payment_method" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>
            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Submit</button>
        </form>
    </div>
</div>
@endsection
