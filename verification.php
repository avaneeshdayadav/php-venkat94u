<?php

require_once dirname(__FILE__) . "/db.php";

if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    $email = htmlspecialchars($_GET['email']);
    $hash = htmlspecialchars($_GET['hash']);
    if (!$conn) {
        die('Connection not Established');
    }
    $Var = $conn->prepare("SELECT * FROM subimages WHERE email =? AND hash = ?");
    $Var->bind_param("ss", $email, $hash);
    $Var->execute();
    $result = $Var->get_result();
    if ($result->num_rows > 0) {
        $Var = $conn->prepare("UPDATE subimages SET verify = 1  WHERE email =? AND hash = ?");
        $Var->bind_param("ss", $email, $hash);
        $Var->execute();
        if ($Var->affected_rows > 0) {
            echo "
                    <script>alert('Verification done successfully');
                    window.location.href = 'http://" . $_SERVER['HTTP_HOST'] . "/assignment/index.php';
                    </script>
                ";
        } else {
            if ($Var->errno == 0) {
                echo "Already Verified";
            } else {
                echo "Verificaiton Failed";
            }
        }
    } else {
        echo "Email Not Found.";
    }
}
