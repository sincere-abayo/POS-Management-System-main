<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600|Raleway:400,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            background: url('the-illustration-graphic-consists-of-abstract-background-with-a-blue-gradient-dynamic-shapes-composition-eps10-perfect-for-presentation-background-website-landing-page-wallpaper-vector.jpg')  center center fixed;
            color:rgb(218, 20, 20);
            font-family: 'Nunito', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .title {
            font-size: 4rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(25, 9, 246, 0.5);
        }

        .links {
            margin: 20px 0;
        }

        .links a {
            color:rgb(239, 170, 32);
            padding: 10px 20px;
            font-size: 2rem;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid transparent;
            border-radius: 5px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .links a:hover {
            background-color:rgb(30, 240, 11);
            color: #1a1a1a;
            border-color:rgb(245, 213, 8);
        }

        @media (max-width: 600px) {
            .title {
                font-size: 3rem;
            }

            .links a {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
        }
    </style>
</head>

<body>
    <div>
        <div class="title">
            <img src="l.jpg">
        </div>

        <div class="links">
            <a href="pos/admin/">Admin Log In</a>
            <a href="pos/cashier/">Cashier Log In</a>
            <a href="pos/customer">Customer Log In</a>
        </div>
    </div>
</body>

</html>