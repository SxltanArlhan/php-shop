
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <title>config</title>
    <style>
        .floating-btn {
                position: fixed;
                bottom: 20px;
                left: 20px;
                background-color: black;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 50px;
                cursor: pointer;
                box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                z-index: 999;
                transition: background-color 0.3s;
            }

            .floating-btn:hover {
                background-color: #333;
            }
    </style>
</head>
<body>

<?php

//comnect database ด้วย my SQL
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "online_shop";

    $dns ="mysql:host=$host;dbname=$database";

 //   $conn = new mysqli($host,$username,$password,$database,);

 //   if($conn -> connect_error){
 //       die(" เชื่อมต่อไม่สำเร็จ : " . $conn->connect_error);
 //   }else{
 //       echo "เชื่อมต่อสำเร็จ";
 //  }

//connect database ด้วย PDO
    try {
        //$conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $conn = new PDO($dns, $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo " PDO : เชื่อมต่อสำเร็จ ";
    } catch(PDOException $e){
        echo " เชื่อมต่อไม่สำเร็จ: " .$e->getMessage();
    }













?>
    <div class="mt-4">
        <a href="index.php"><button class="floating-btn">Back</button></a>
    </div>
</body>
</html>