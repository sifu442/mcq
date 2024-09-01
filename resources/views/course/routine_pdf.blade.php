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
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}" type="text/css">
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
        
    </table>

    <div class="margin-top">
        <table class="products">
            <thead>
                <tr>
                    <th>Exam Name</th>
                    <th>Date</th>
                    <th>Day of Week</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examInfo as $exam)
                <tr class="items">
                    <td>{{ $exam['examName'] }}</td>
                    <td>{{ $exam['date'] }}</td>
                    <td>{{ $exam['dayOfWeek'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer margin-top">
        <div>Thank you</div>
        <div>&copy; MCQ</div>
    </div>
</body>

</html>
