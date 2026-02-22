<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Email</title>
    <style>
        /* Global Styles */

        .tablestyle{

                
        border-style: solid;

        }

        thead {
        background-color: #f2f2f2;

        }
        table {
        border-collapse:collapse;
        max-width:100%;
        margin: 0 auto;
        text-align: left;
        }
        th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        }

        tr:nth-child(even) {
        background-color: #f2f2f2;
        }
        tr:hover {
        background-color: #ddd;
        }
       
        h2{
            text-align:center;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #800000; /* Updated to maroon */
        }

        /* Header Styles */
        .header {
            background-color: #800000; /* Maroon header */
            color: #ffffff; /* Text color changed to white */
            padding: 20px;
            text-align: center;
        }

        /* Content Styles */
        .content {
            padding: 20px;
            background-color: #ffffff;
            color: black; /* Text color changed to black */
            border-radius: 10px; /* Increased border radius for a softer look */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Increased shadow depth */
            text-align: center; /* Center-align content */
        }

        /* Button Styles */
        .button {
            padding: 15px 30px; /* Increased padding for a larger button */
            background-color: #800000; /* Maroon button color */
            color: #ffffff; /* Text color changed to white */
            text-decoration: none;
            border-radius: 5px; /* Rectangular button */
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #5c0000; /* Darker maroon color on hover */
        }

        /* Footer Styles */
        .footer {
            background-color: #800000; /* Maroon footer */
            color: #ffffff; /* Text color changed to white */
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        
        <h1 style="font-size: 24px; margin-top: 10px;">Welcome to the On-the-job Training Information Management System</h1>
    </div>

    <!-- Content Section -->
    <div class="content">
    
        <h2>Student OJT Information</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Company Name</th>
                    <th>Company Address</th>
                    <th>Nature of Business</th>
                    <th>Nature of Networking or Linkages</th>
                    <th>Level</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reporting Time</th>
                    <th>Contact Name</th>
                    <th>Position of Contact</th>
                    <th>Contact Number of Representative</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($studentData as $data)
                <tr>
                    <td>{{ $data['student']->full_name }}</td>
                    <td>{{ $data['ojt']->company_name }}</td>
                    <td>{{ $data['ojt']->company_address }}</td>
                    <td>{{ $data['ojt']->nature_of_bus }}</td>
                    <td>{{ $data['ojt']->nature_of_link }}</td>
                    <td>{{ $data['ojt']->level }}</td>
                    <td>{{ $data['ojt']->start_date }}</td>
                    <td>{{ $data['ojt']->finish_date }}</td>
                    <td>{{ $data['ojt']->report_time }}</td>
                    <td>{{ $data['ojt']->contact_name }}</td>
                    <td>{{ $data['ojt']->contact_position }}</td>
                    <td>{{ $data['ojt']->contact_number }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>
            <a href="#">Privacy</a> | <a href="#">General terms and conditions</a> | <a href="#">Unsubscribe</a><br>
            PUP Taguig Branch, General Santos Ave, Lower Bicutan, Taguig, Metro Manila, Philippines | University<br>
            &copy; POLYTECHNIC UNIVERSITY OF THE PHILIPPINES
        </p>
    </div>
</body>
</html>