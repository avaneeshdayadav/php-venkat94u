<?php
require_once dirname(__FILE__) . "/db.php";

$alert = '';

if (isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['sname']) && !empty($_POST['sname'])) {
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['sname']);
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $hash = md5($randomString); //random strings for hash
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);//connecting to Data Base
    if (!$conn) {
        die('Connection not Established');
    }

    $Var = $conn->prepare("SELECT * FROM subimages WHERE email = ?");
    $Var->bind_param("s", $email);
    $Var->execute();
    $result = $Var->get_result();
    if ($result->num_rows == 0) {
        $Var = $conn->prepare("INSERT INTO subimages (email, hash,username) VALUES (?,?,?)");
        $Var->bind_param("sss", $email, $hash, $username);

        $Var->execute();
        if ($Var->affected_rows > 0) {
            $to = $email;
            $subject = "Xkcd Comics Subscription ";
            $url = "http://" . $_SERVER['HTTP_HOST'] . "/assignment/verification.php?email=$email&hash=$hash";
            $msg ="
                        <html>
                        <head>
                            <title>Subscription Email</title>
                        </head>
                        <body style='background-color:tomato;'>
                            <h1 style='color:#ff4500;'>Dear $username: </h1>
                            <h3 style='color:#dc143c;'>Please verify your email by clicking the link below <br> <br>
                            <a target='_blank' href=$url>Click to Verify</a> </h3>
                            <h2 style='color:black;' >After verifying your email you will be subscribed to Xkcd Comics and you will receive emails for every five minutes.Please Subscribe now and enjoy the Day</p></h2>
                            <h4 style='color:#ff00ff;'>With regards </h4>
                            <h5 style='color:#9400d3;'>Xkcd Team</h5>

                        </body>
                    </html>
                ";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From:venkatguptha8750@gmail.com" . "\r\n";

            mail($to, $subject, $msg, $headers);

            $alert = 'Verification Mail sent to your email address.';
        } else {
            echo "Error Occured";
        }
    } else {
        $alert = "Email already registered";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XKCD Challenge</title>
    <link rel="stylesheet" href="xkcdstyles.css">
</head>

<body>
    <?php
    if (!empty($alert)) {
        echo "
                <script>alert('$alert');</script>
            ";
    }

    ?>
    <div>
        <h1 align="center">SUBSCRIBE TO XKCD COMICS</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <h3>User Name:</h3>
                <input type="text" name="sname" placeholder="Enter your name" required>
                <h3>Email address:</h3>
                <input type="email" name="email" placeholder="Enter your email address" required>

                <p style="font-size:30px">Please Subscribe and Have Fun.
                <p>
            </div>
            <button type="submit">Submit</button>
        </form>
        <div>

</body>

</html>
