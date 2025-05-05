<?php 
session_start(); 

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Coding</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="../../assets/css/style.css" rel="stylesheet" type="text/css">
    <style>
        button {
            background-color: #006699 !important;
            color: white !important;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>

</head>

<body>

    <div class="container">
        <aside class="sidebar">
            <p>Halaman Profile</p>

        </aside>

        <div class="main-content">
            <div class="filter">
                <a href="<?php echo $host; ?>/authentication/lab-1"><button type="button" class="btn btn-outline-primary">Back</button></a>
            </div>

            <div class="login-container">
                Selamat datang, <?php echo $_SESSION['username']; ?>=
            </div>
        </div>
    </div>

</body>

</html>