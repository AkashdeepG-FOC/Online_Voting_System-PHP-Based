<?php
include "db_connect.php"; // ✅ Use proper database connection

// Securely update password
$hashed_password = password_hash('gascadmin', PASSWORD_DEFAULT);
$sql = "UPDATE users SET password = ? WHERE username = 'admin'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Admin password updated successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating password: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
