<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily Report</title>

    <style>
        body,
        table,
        td,
        p {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        body {
            background-color: #f4f6fb;
            line-height: 1.6;
        }

        .container {
            max-width: 775px;
            margin: 0px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e6e9f2;
        }

        .header {
            text-align: center;
            padding-bottom: 18px;
            margin-bottom: 22px;
            border-bottom: 1px solid #e9ecf5;
        }

        .header h2 {
            margin-top: 6px;
            font-size: 22px;
            font-weight: 600;
            color: #2c3e66;
        }

        .info {
            margin-bottom: 18px;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th {
            background-color: #f1f4fb;
            font-weight: 600;
            font-size: 14px;
            padding: 12px;
            border: 1px solid #e4e7f0;
            text-align: left;
        }

        td {
            padding: 12px;
            font-size: 14px;
            border: 1px solid #e4e7f0;
        }

        tr:nth-child(even) td {
            background-color: #fafbff;
        }

        .total-row td {
            background-color: #f6f8fe !important;
            font-weight: 600;
        }

        .footer {
            margin-top: 26px;
            text-align: center;
            font-size: 13px;
            color: #888;
            border-top: 1px solid #e9ecf5;
            padding-top: 14px;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <h2>
                Daily Work Report -
                {{ Auth::guard('employee')->user()->employee_name }}
            </h2>
        </div>

        <div class="info">
            @if (count($reports) > 0)
                <p>
                    <strong>Work Date:</strong>
                    {{ \Carbon\Carbon::parse($reports[0]['DrEntryDate'])->format('d M Y') }}
                </p>
                <p>
                    <strong>Submitted On:</strong>
                    {{ \Carbon\Carbon::parse($reports[0]['CreationDate'])->format('d M Y') }}
                </p>
            @else
                <p><strong>Work Date:</strong> -</p>
                <p><strong>Submitted On:</strong> {{ date('d M Y') }}</p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Work Details</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Hours</th>
                </tr>
            </thead>

            <tbody>
                @php $totalMinutes = 0; @endphp

                @foreach ($reports as $r)
                    @php
                        [$h, $m] = explode(':', $r['TotalHours'] ?? '00:00');
                        $totalMinutes += $h * 60 + $m;
                        $projectName = 'N/A';
                        if (!empty($r['ProjectID'])) {
                            $project = \App\Models\Project::find($r['ProjectID']);
                            if ($project) {
                                $projectName = $project->project_name;
                            }
                        }
                        $startTime = !empty($r['StartingTime'])
                            ? \Carbon\Carbon::createFromFormat('H:i', $r['StartingTime'])->format('h:i A')
                            : '-';

                        $endTime = !empty($r['EndingTime'])
                            ? \Carbon\Carbon::createFromFormat('H:i', $r['EndingTime'])->format('h:i A')
                            : '-';
                    @endphp

                    <tr>
                        <td>{{ $projectName }}</td>
                        <td>{{ $r['WorkDescription'] ?? 'N/A' }}</td>
                        <td>{{ $startTime }}</td>
                        <td>{{ $endTime }}</td>
                        <td>{{ $r['TotalHours'] ?? '00:00' }}</td>
                    </tr>
                @endforeach

                @php
                    $totalH = floor($totalMinutes / 60);
                    $totalM = $totalMinutes % 60;
                @endphp

                <tr class="total-row">
                    <td colspan="4" style="text-align:right;">Total Hours</td>
                    <td>{{ $totalH }} Hr : {{ $totalM }} Min</td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            © {{ date('Y') }} Neural Info Solutions Private Limited. All rights reserved.
        </div>

    </div>

</body>

</html>
