<?php

reset($_FILES);
$temp = current($_FILES);
$imageFolder = "public/images/dl/";
$relative_path = "public/images/dl/";
$maxFileSize = $_POST['MAX_FILE_SIZE'];
$fileSize = $temp['size'];

if (is_uploaded_file($temp['tmp_name']) && $fileSize < $maxFileSize) {
    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), $arrAcceptedExtention)) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    $fileName = md5(uniqid(rand(), true)) . '.' . pathinfo($temp['name'], PATHINFO_EXTENSION);
    $filetowrite = $imageFolder . $fileName;
    $final_path = $relative_path . $fileName;
    move_uploaded_file($temp['tmp_name'], $filetowrite);
  
    echo json_encode(array('location' => $final_path));
} else {
    header("HTTP/1.1 500 Server Error");
}
