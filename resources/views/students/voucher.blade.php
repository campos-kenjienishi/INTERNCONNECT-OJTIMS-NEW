<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .coupon {
            background-image: url('/images/OJTIMS.jpg');
            background-size: cover;
            background-repeat: no-repeat;  
            height: 250px;
            width: 400px;
            margin-left: 22%;
            text-align: center; /* Align text in the center horizontally */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h1 {
            margin-top: 0; /* Reset margin-top */
            margin-left: 32%;
            color: maroon;
            font-size: 35px;
        }

        @media print {
            .coupon{
        background-image: url('/images/OJTIMS.jpg');
    }
}
    </style>
</head>
<body>
    <div class="coupon">
         @foreach ($company->vouchers as $vouchers)
         <h1> {{ $vouchers->filename }}</h1>
        @endforeach
    </div>
</body>
</html>
