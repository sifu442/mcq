<x-app-layout>

        <div class="">
            <div class="flex">
                <div class="p-8">
                    <h2 class="block mt-1 text-lg leading-tight font-medium text-black">{{ $course->name }}</h2>
                    <p class="mt-2 text-gray-500">Fee: {{ $course->price }}</p>
                    <p class="mt-2 text-gray-500">User: {{ $user->name }}</p>
                    <p class="mt-2 text-gray-500">User ID: {{ $user->id }}</p>
                    <form method="POST" action="{{ route('course.purchase', ['course' => $course->id]) }}">
                        @csrf
                        <div class="mt-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <input type="text" name="payment_method" id="payment_method" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        <div class="mt-4">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-app-layout>
