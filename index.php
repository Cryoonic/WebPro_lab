<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>listy</title>
    <style>
        body{
            background-color: whitesmoke;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            margin: 0;
        }
        .wrapper{
            max-width: 1100px;
            margin: auto;
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgb(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .image-section {
            flex: 1;
            background: f5f5f5;
            padding: 20px;
        }

        .image-section img {
        max-width: 100%;
        height: auto;
        }

        .main-section{
            padding: 20px;
        }

        h1,h3{
            text-align: center;
        }

        button{
            width: 400px;
            height: 25px;
            background-color: cyan;
            border: none;
            margin-right: 20px;
        }

        button a{
            text-decoration: none;
            color: black;
        }

        a{
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="image-section">
            <img src="../WebPro_lab/asset/3df22b1e-1a2c-4d1c-be20-a266833a51b2.webp" alt="">
        </div>
        <div class="main-section">
            <h1>Productive Mind</h1>
            <p>With only feature you need, listy is customized
                for  <br>individuals seeking stress-free way to stay focused on <br>
                their goals, project, and tasks.
            </p>
            <button><a href="../WebPro_lab/auth/signup.php">Get Started</a></button>
            <h3>Already have an account? <a href="../WebPro_lab/auth/login.php">Sign In</a></h3>
        </div>
    </div>
</body>
</html>