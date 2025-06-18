<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized. Please login.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['account_photo'])) {
    
    // Check for upload errors
    if ($_FILES['account_photo']['error'] !== UPLOAD_ERR_OK) {
        if ($_FILES['account_photo']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['account_photo']['error'] == UPLOAD_ERR_FORM_SIZE) {
            echo "File too large. Max size allowed is 2MB.";
            exit();
        }
        echo "File upload error.";
        exit();
    }
    
    // Validate file size (extra safety in PHP, since JS checks before)
    if ($_FILES['account_photo']['size'] > 2 * 1024 * 1024) {
        echo "File too large. Max size allowed is 2MB.";
        exit();
    }
    
    $fileTmpPath = $_FILES['account_photo']['tmp_name'];
    $fileType = $_FILES['account_photo']['type'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($fileType, $allowedTypes)) {
        echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        exit();
    }

    // Read the uploaded file into a variable
    $imgData = file_get_contents($fileTmpPath);

    include 'connection.php'; // Make sure $conn is available

    // Check if user_details row exists
    $stmt = $conn->prepare("SELECT userDetails_id FROM USER_DETAILS WHERE user_id = ?");
    if (!$stmt) {
        echo "Database error: " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update image if user exists
        $sql = "UPDATE USER_DETAILS SET image = ? WHERE user_id = ?";
        $stmt2 = $conn->prepare($sql);
        if (!$stmt2) {
            echo "Database error: " . $conn->error;
            exit();
        }

        $null = NULL;
        $stmt2->bind_param("bi", $null, $user_id);
        $stmt2->send_long_data(0, $imgData);

        if (!$stmt2->execute()) {
            echo "Execute failed: " . $stmt2->error;
            exit();
        }

        $stmt2->close();
    } else {
        // Insert new record if user_details does not exist
        $defaultFname = "FirstName";
        $defaultLname = "LastName";

        $sql = "INSERT INTO USER_DETAILS (fname, lname, image, user_id) VALUES (?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql);
        if (!$stmt2) {
            echo "Database error: " . $conn->error;
            exit();
        }

        $null = NULL;
        $stmt2->bind_param("ssbi", $defaultFname, $defaultLname, $null, $user_id);
        $stmt2->send_long_data(2, $imgData);

        if (!$stmt2->execute()) {
            echo "Execute failed: " . $stmt2->error;
            exit();
        }

        $stmt2->close();
    }

    $stmt->close();
    $conn->close();

    // Return success message, no redirect
    echo "success";
    exit();
} else {
    echo "No image uploaded.";
    exit();
}
