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

    @if (empty($studentData))
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
                @foreach ($studentData as $data)
                    <tr class="tablestyle">
                        <td class="tablestyle">{{ $data['student']->full_name }}</td>
                        <td class="tablestyle">{{ $data['ojt']->company_name }}</td>
                        <td class="tablestyle">{{ $data['ojt']->company_address }}</td>
                        <td class="tablestyle">{{ $data['ojt']->nature_of_bus }}</td>
                        <td class="tablestyle">{{ $data['ojt']->nature_of_link }}</td>
                        <td class="tablestyle">{{ $data['ojt']->level }}</td>
                        <td class="tablestyle">{{ $data['ojt']->start_date }}</td>
                        <td class="tablestyle">{{ $data['ojt']->finish_date }}</td>
                        <td class="tablestyle">{{ $data['ojt']->report_time }}</td>
                        <td class="tablestyle">{{ $data['ojt']->contact_name }}</td>
                        <td class="tablestyle">{{ $data['ojt']->contact_position }}</td>
                        <td class="tablestyle">{{ $data['ojt']->contact_number }}</td>
                        
                    </tr>
              
            </tbody>
            @endforeach
        </table>

        
    @endif
</body>
</html>