<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

        .container {
            width: 100%;
            border: 1px solid black;
        }

        .header {
            text-align: center;
            background-color: #007300;
            color: white;
            padding: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        td:nth-child(1) {
            width: 5%;
        }

        td:nth-child(2) {
            width: 15%;
        }

        td:nth-child(3) {
            width: 80%;
        }

        th {
            background-color: #005500;
            color: white;
        }

        .row-green {
            background-color: #DFFFE0;
        }

        .footer {
            font-size: 14px;
            margin-top: 10px;
        }
        .underline{
            text-decoration: underline;
        }
    </style>
    <title>{{ $course->title }}</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h4 class="underline">Bismillah-ir Rahman-ir Rahim</h4>
            <h1 class="underline">Plan English Master Book</h1>
            <p class="underline">Advance preparation is the key to success</p>
            <p class="underline">Course During - {{ $course->time_span }} Months | Total Exam - 65</p>
            <p class="underline">Total Course Fee only {{ $course->price }} Taka - Offer Price {{ $course->discounted_price }} Taka</p>
            <p class="underline">Total Marks – 35+ | Time – 20 Minutes</p>
            <p class="underline">Live Exam Time: 24+ Hours | Model Test - 80+ & Time - 45+ Minutes</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Date</th>
                    <th>Content</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($examInfo as $index => $exam)
                <tr class="{{ $index % 2 === 1 ? 'row-green' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $exam['date'] }}</td>
                    <td>
                        {!! $course->exams[$index]->syllabus !!}
                    </td>
                </tr>
                @endforeach
                <!-- Add more rows following the structure above -->
            </tbody>
        </table>
    </div>

</body>

</html>
