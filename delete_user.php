<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $id = intval($_POST['user_id']);

    $stmt = $conn->prepare("UPDATE USERS SET is_deleted = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User marked as deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
