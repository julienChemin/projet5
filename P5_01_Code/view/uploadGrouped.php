<?php

$temp = next($_FILES);
$imageFolder = "public/images/dl/";
$relative_path = "public/images/dl/";
$maxFileSize = 6000000;

if (!$temp) {
    return false;
}

$fileSize = $temp['size'];

if (is_uploaded_file($temp['tmp_name']) && $fileSize < $maxFileSize) {
    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), $arrAcceptedExtention)) {
        return $finalPath = false;
    }
    do {
        $fileName = md5(uniqid(rand(), true)) . '.' . pathinfo($temp['name'], PATHINFO_EXTENSION);
    } while (file_exists($imageFolder . $fileName));
    
    $filetowrite = $imageFolder . $fileName;
    $finalPath = $relative_path . $fileName;
    move_uploaded_file($temp['tmp_name'], $filetowrite);

    return $finalPath;
} else {
    return $finalPath = false;
}