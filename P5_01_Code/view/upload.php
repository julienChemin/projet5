<?php

reset($_FILES);
$temp = current($_FILES);

if (!isset($imageFolder)) {
    $imageFolder = "public/images/dl/";
    $relative_path = "public/images/dl/";
}

$maxFileSize = 6000000;
$fileSize = $temp['size'];

if (is_uploaded_file($temp['tmp_name']) && $fileSize < $maxFileSize) {
    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), $arrAcceptedExtention)) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }
    do {
        $fileName = md5(uniqid(rand(), true)) . '.' . pathinfo($temp['name'], PATHINFO_EXTENSION);
    } while (file_exists($imageFolder . $fileName));
    
    $filetowrite = $imageFolder . $fileName;
    $final_path = $relative_path . $fileName;
    move_uploaded_file($temp['tmp_name'], $filetowrite);
  
    if (!isset($ajax)) {
        echo json_encode(array('location' => $final_path));
    }
} else if (!isset($ajax)) {
    header("HTTP/1.1 500 Server Error");
} else {
    return false;
}
