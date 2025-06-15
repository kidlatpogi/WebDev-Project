<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

include 'connection.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT image FROM USER_DETAILS WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

$image = null;
if ($stmt->num_rows > 0) {
    $stmt->bind_result($image);
    $stmt->fetch();
}

$stmt->close();
$conn->close();

if (!empty($image)) {
    // Detect MIME type from blob
    $finfo = finfo_open();
    $mimeType = finfo_buffer($finfo, $image, FILEINFO_MIME_TYPE);
    finfo_close($finfo);
    
    header("Content-Type: $mimeType");
    echo $image;
} else {
    // fallback
    header("Content-Type: image/png");
    readfile("photos/sample_account_photo.png");
}
?>
