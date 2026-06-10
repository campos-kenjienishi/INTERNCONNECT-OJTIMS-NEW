<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Report</title>

    <style type="text/css">
        h1{
            text-align:center;
        }

        table{
            
            border-collapse:collapse;
            width:100%;
            margin: 0 auto;
            /* text-align: left; */
        }

        .tablestyle{
            border-style: solid;

        }

        thead tr .tablestyle {
            font-size: 12px;
            text-align: center;
            padding: 3px;
            padding-left: 6px;
            padding-right: 6px;
        }

        tbody tr .tablestyle {
            font-size: 10px;
            text-align: center;
            padding: 3px;
            padding-left: 6px;
            padding-right: 6px;
        }


    </style>

</head>

<body>
    <h1>OJT Information Report</h1>

    @php
        $studentRows = collect($studentData ?? [])->filter(function ($item) {
            return !empty($item['student']);
        });
    @endphp

    @if ($studentRows->isEmpty())
        <p>No OJT records found for the selected options.</p>
    @else
        <table class="tablestyle">
            <thead class="tablestyle">
                <tr class="tablestyle">
                    <th class="tablestyle">Full Name</th>
                    <th class="tablestyle">Company Name</th>
                    <th class="tablestyle">Company Address</th>
                    <th class="tablestyle">Nature of Business</th>
                    <th class="tablestyle">Nature of Networking or Linkages</th>
                    <th class="tablestyle">Level</th>
                    <th class="tablestyle">Start Date</th>
                    <th class="tablestyle">End Date</th>
                    <th class="tablestyle">Reporting Time</th>
                    <th class="tablestyle">Contact Name</th>
                    <th class="tablestyle">Position of Contact</th>
                    <th class="tablestyle">Contact Number of Representative</th>
                    
                </tr>
            </thead>

            <tbody class="tablestyle">
                @foreach ($studentRows as $data)
                    @php $ojt = $data['ojt'] ?? null; @endphp
                    <tr class="tablestyle">
                        <td class="tablestyle">{{ $data['student']->full_name }}</td>
                        <td class="tablestyle">{{ $ojt->company_name ?? 'No OJT record yet' }}</td>
                        <td class="tablestyle">{{ $ojt->company_address ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->nature_of_bus ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->nature_of_link ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->level ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->start_date ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->finish_date ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->report_time ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->contact_name ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->contact_position ?? '-' }}</td>
                        <td class="tablestyle">{{ $ojt->contact_number ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
