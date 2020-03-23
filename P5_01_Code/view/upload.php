<?php

$imageFolder = "public/images/";
$relative_path = "public/images/";

reset($_FILES);
$temp = current($_FILES);

if (is_uploaded_file($temp['tmp_name'])) {
    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("jpeg", "jpg", "png"))) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    $fileName = $_SESSION['pseudo'] . md5(uniqid(rand(), true)) . '.' . pathinfo($temp['name'], PATHINFO_EXTENSION);
    $filetowrite = $imageFolder . $fileName;
    $final_path = $relative_path . $fileName;
    move_uploaded_file($temp['tmp_name'], $filetowrite);
  
    echo json_encode(array('location' => $final_path));
} else {
    header("HTTP/1.1 500 Server Error");
}