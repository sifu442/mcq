<x-app-layout>
    <div class="">
        <div class="">
            <div class="p-8">
                <h2 class="block mt-1 text-lg leading-tight font-medium text-black">{{ $course->name }}</h2>
                <p class="mt-2 text-gray-500">Course: {{ $course->title }}</p>
                <p class="mt-2 text-gray-500">Fee: {{ $course->price }}</p>
                <p class="mt-2 text-gray-500">Student: {{ $user->name }}</p>
                <p class="mt-2 text-gray-500">Roll: {{ $user->id }}</p>
                <form method="POST" action="{{ route('course.purchase', ['course' => $course->id]) }}">
                    @csrf
                    <div class="mt-4">
                        <label for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select an
                            option</label>
                        <select id="countries"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected>Choose a country</option>
                            <option value="Bkash">Bkash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number"
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Purchase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
