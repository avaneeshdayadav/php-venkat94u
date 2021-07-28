<?php

require_once dirname(__FILE__) . "/db.php";

if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
    $email = $_GET['email'];
    $hash = $_GET['hash'];
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    if (!$conn) {
        die('Connection not Established');
    }
    $verify = 1;
    $Var = $conn->prepare("SELECT * FROM subimages WHERE email = ? AND hash = ? AND verify = ?");
    $Var->bind_param("sss", $email, $hash, $verify);
    $Var->execute();
    $result = $Var->get_result();
    if ($result->num_rows > 0) {
        $Var = $conn->prepare("DELETE FROM subimages WHERE email =? AND hash =? ");
        $Var->bind_param("ss", $email, $hash);
        $Var->execute();
        if ($Var->affected_rows > 0) {
            echo "
                    <script>alert('Unsubscribe done successfully');
                    window.location.href = 'index.php';
                    </script>
                ";
        } else {
            echo "Unsubscribe Failed";
        }
    } else {
        echo "User Not Found.";
    }
}
