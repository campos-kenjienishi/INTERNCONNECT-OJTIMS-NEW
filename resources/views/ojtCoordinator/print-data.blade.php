<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style type="text/css">
        table{
            border-style: solid;
            border-collapse:collapse;
            width:70%;
            margin: 0 auto;
            text-align: left;
        }
        tr th{

           
            border-style: solid;

        }

        tr td{

            border-style: solid;

        }
        </style>
</head>
<body>
    <div class="details">
    <h1 style="text-align: center;">Company Data</h1>

<table>






    <thead>
        <tr style=" background:#eee;">
        
            <td>Company Name</td>
            <td>Company Address</td>
            <td>Company Representative</td>
            <td>Company Contact No.</td>
            <td>Company Email</td>
            <td>Student List</td>


        </tr>
    </thead>

    <tbody>
       
        <tr>
            <td>{{$company->company_name}}</td>
            <td>{{$company->company_address}}</td>
            <td>{{$company->company_rep}}</td>
            <td>{{$company->companyNo}}</td>
            <td>{{$company->company_email}}</td>
            
            <td>
                @foreach ($company->students as $student)
                    <li>{{ $student->full_name }}</li>
                @endforeach
            </td>

  
            
     
      </tr>

    </tbody>
</table>




</div>













</body>
</html>
