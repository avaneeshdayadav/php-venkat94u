<?php

require_once dirname(__FILE__) . "/db.php";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {

    die("Connection not Established");
}

$url_curr_xkcd = "http://xkcd.com/info.0.json";
$data = file_get_contents($url_curr_xkcd);
$json_data = json_decode($data, true);
$curr_num = $json_data["num"];
$rand = rand(1, $curr_num);
$url_random_xkcd = "https://xkcd.com/" . $rand . "/info.0.json";
$data = file_get_contents($url_random_xkcd);
$json_data = json_decode($data, true);
$image = $json_data['img'];
$Comic_image_path = "xkcd images/image.png";
file_put_contents($Comic_image_path, file_get_contents($image));


$subject = $json_data['safe_title'];

$verify = 1;

$Var = $conn->prepare("SELECT * FROM subimages WHERE verify = ?");
$Var->bind_param("i", $verify);
$Var->execute();
$results = $Var->get_result();

foreach ($results as $row) {

    $to = $row['email'];
    $hash = $row['hash'];
    $username = $row['username'];

    $htmlContent = "
                <html>
                    <head>
                        <title>XKCD Emoticon</title>
                    </head>
                    <body>
                        <h2 style='color:#3cb371;' >HELLO $username:</h2>
                        <h3>Enjoy your Comic </h1>
                        <center>
                            <h1 style='color:#7cfc00;'>" . $json_data['title'] . "</h1>
                            <img src='" . $image . "' alt='" . $json_data['alt'] . "'>
                            <a href='http://" .$servername. "/assignment/unsubscription.php?email=$to&hash=$hash'><h3>Unsubscribe XKCD</h3></a>
                        </center><br/>
                        <p>With regards </p>
                        <p>Xkcd Team</p>
                    </body>
                </html>
            ";

    $headers = "From: venkatguptha8750@gmail.com";

    $semi_rand = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";
    if (!empty($Comic_image_path)) {
        if (file_exists($Comic_image_path)) {
            $message .= "--{$mime_boundary}\n";
            $fp = @fopen($Comic_image_path, "rb");
            $data = @fread($fp, filesize($Comic_image_path));

            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"" . basename($Comic_image_path) . "\"\n" .
                "Content-Description: " . basename($Comic_image_path) . "\n" .
                "Content-Disposition: attachment;\n" . " filename=\"" . basename($Comic_image_path) . "\"; size=" . filesize($Comic_image_path) . ";\n" .
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        } else {
            echo "File Not Found";
        }
    } else {
        echo "Image Url Empty";
    }

    $message .= "--{$mime_boundary}--";

    $mail_result = mail($to, $subject, $message, $headers);
    echo $mail_result ? "<h1>Email Sent Successfully!</h1>" : "<h1>Email sent failed.</h1>";
}
