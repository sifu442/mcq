<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        @font-face {
            font-family: 'Noto Sans Bengali';
            font-style: normal;
            font-weight: 400;
            src: url('{{ public_path('fonts/NotoSansBengali-Regular.ttf') }}') format('truetype');
        }

        body {
            font-family: 'Noto Sans Bengali', sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('') }}" type="text/css">
    <title>{{ $course->title }}</title>
</head>

<body>
    <h1>{{ $course->title }}</h1>

    <h2>Subjects:</h2>
    <ul>
        @foreach ($subjects as $subject)
            <li>{{ $subject->name }}</li>
        @endforeach
    </ul>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ asset('laraveldaily.png') }}" alt="laravel daily" width="200" />
            </td>
            <td class="w-half">
                <h2>Invoice ID: 834847473</h2>
            </td>
        </tr>
    </table>

    <div class="margin-top">
        <table class="products">
            <tr>
                <th>Exam Name</th>
                <th>Date</th>
                <th>Price</th>
            </tr>
            <tr class="items">
                @foreach($data as $item)
                    <td>
                        {{ $exam['examName'] }}
                    </td>
                    <td>
                        {{ $exam['date'] }} ({{ $exam['dayOfWeek'] }})
                    </td>
                    <td>
                        {{ $exam['dayOfWeek'] }}
                    </td>
                @endforeach
            </tr>
        </table>
    </div>


    <div class="footer margin-top">
        <div>Thank you</div>
        <div>&copy; MCQ</div>
    </div>
</body>
</html>
