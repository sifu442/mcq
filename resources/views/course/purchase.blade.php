<x-app-layout>
    <div class="course-details">
        <h2 class="block mt-1 text-lg leading-tight font-medium text-black">{{ $course->name }}</h2>
        <p class="mt-2 text-gray-500">Course: {{ $course->title }}</p>
        <p class="mt-2 text-gray-500">Fee: {{ $course->price }} TK</p>
        <p class="mt-2 text-gray-500">Student: {{ $user->name }}</p>
        <p class="mt-2 text-gray-500">Roll: {{ $user->id }}</p>
    </div>

    <div class="payment-details flex justify-between pt-5">
        <div href="#" class="flex flex-col items-center  border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="https://download.logo.wine/logo/BKash/BKash-Icon-Logo.wine.png" alt="Bkash-logo">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Bkash</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Marchent</p>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">01886886640</p>
            </div>
        </div>
        <div href="#" class="flex flex-col items-center border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="https://download.logo.wine/logo/Nagad/Nagad-Vertical-Logo.wine.png" alt="Nagad-logo">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Nagad</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Marchent</p>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">01886886640</p>
            </div>
        </div>
        <div href="#" class="flex flex-col items-center border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="https://seeklogo.com/images/D/dutch-bangla-rocket-logo-B4D1CC458D-seeklogo.com.png" alt="Rocket">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Rocket</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Marchent</p>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">01886886640</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('course.purchase', ['course' => $course->id]) }}">
        @csrf
        <div class="mt-4">
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a payment method</label>
            <select id="countries"
                name="payment_method"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="Bkash">Bkash</option>
                <option value="Nagad">Nagad</option>
                <option value="Rocket">Rocket</option>
            </select>
        </div>
        <div class="mt-4">
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" name="phone_number" id="phone_number"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " required />
                <label for="phone_number"
                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Phone
                    number (019*******)</label>
            </div>
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
</x-app-layout>
