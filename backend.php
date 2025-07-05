<?php
session_start();
include "db_connect.php"; // Ensure database connection is included

// ✅ DEBUG: Log every request to backend.php
error_log("backend.php accessed");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ✅ DEBUG: Log username & password (hash password before storing in DB)
    error_log("Login attempt for username: $username");

    if (empty($username) || empty($password)) {
        send_response("error", "Username and password cannot be empty.");
    }

    // Fetch user from database
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        send_response("error", "Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // ✅ DEBUG: Log user role
        error_log("User found: " . print_r($row, true));

        if (password_verify($password, $row['password'])) { // Check password
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            $redirectPage = ($row['role'] === 'admin') ? "admin_dashboard.html" : "vote.html";

            send_response("success", "Login successful!", $redirectPage);
        } else {
            send_response("error", "Invalid username or password.");
        }
    } else {
        send_response("error", "User not found.");
    }
}

// Function to send JSON response
function send_response($status, $message, $redirect = null) {
    header("Content-Type: application/json");
    $response = ["status" => $status, "message" => $message];

    if (!empty($redirect)) { // ✅ Add redirect link if available
        $response["redirect"] = $redirect;
    }

    // ✅ DEBUG: Log what response is sent to frontend
    error_log("Response sent: " . json_encode($response));

    echo json_encode($response);
    exit;
}


// ✅ ADD USER FUNCTION (Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // ✅ Check Admin Access
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        send_response("error", "Unauthorized access!");
    }

    // ✅ Sanitize Inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($password) || empty($role)) {
        send_response("error", "All fields are required!");
    }

    // ✅ Hash Password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Check if Username Exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        send_response("error", "Username already exists!");
    }
    mysqli_stmt_close($check_stmt);

 // ✅ Handle Image Upload (Only for Candidates)
$image_filename = NULL;
if ($role === 'candidate' && isset($_FILES['profileImg']) && $_FILES['profileImg']['error'] === UPLOAD_ERR_OK) {

    $upload_dir = __DIR__ . "/uploads/";

    // ✅ Ensure Upload Folder Exists
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        send_response("error", "Failed to create uploads directory.");
        exit();
    }
    if (!is_writable($upload_dir)) {
        send_response("error", "Upload directory is not writable!");
        exit();
    }

    // ✅ Process Image
    $image_tmp = $_FILES['profileImg']['tmp_name'];  // FIXED: Corrected the $_FILES key
    $image_size = $_FILES['profileImg']['size'];
    $image_ext = strtolower(pathinfo($_FILES['profileImg']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($image_ext, $allowed_ext)) {
        send_response("error", "Invalid image format. Allowed: jpg, jpeg, png, gif, webp.");
        exit();
    }

    if ($image_size > 2 * 1024 * 1024) { // 2MB Limit
        send_response("error", "Image size must be less than 2MB.");
        exit();
    }

    // ✅ Generate Unique Filename
    $image_filename = uniqid("img_", true) . "." . $image_ext;
    $image_path = $upload_dir . $image_filename;

    // ✅ Move Uploaded File
    if (!move_uploaded_file($image_tmp, $image_path)) {
        error_log("Failed to move uploaded file: " . $_FILES['profileImg']['error']);  // FIXED: Corrected logging
        send_response("error", "Failed to upload the image.");
        exit();
    }
}

// ✅ Insert into Database
if ($role === 'candidate') {
    error_log("Adding candidate: $username");
    $sql = "INSERT INTO candidates (username, password, image) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $password_hashed, $image_filename);
} else {
    error_log("Adding user: $username with role: $role");
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $password_hashed, $role);
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    send_response("success", "User added successfully!");
} else {
    mysqli_stmt_close($stmt);
    send_response("error", "Error: " . mysqli_error($conn));
}

}

// ✅ Function to Send JSON Response




// ✅ DELETE USER FUNCTION (Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        send_response("error", "Unauthorized access!");
    }

    $username = trim($_POST['username']);
    if (empty($username)) {
        send_response("error", "Username is required!");
    }

    $sql = "DELETE FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);

    if (mysqli_stmt_execute($stmt)) {
        send_response("success", "User deleted successfully!");
    } else {
        send_response("error", "Error: " . mysqli_error($conn));
    }
}

// ✅ GET USERS FUNCTION (Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_users'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        send_response("error", "Unauthorized access!");
    }

    $sql = "SELECT username, role FROM users";
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    echo json_encode($users);
    exit;
}

// ✅ LOGOUT FUNCTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    send_response("success", "Logged out successfully!");
}

// ✅ VOTING FUNCTION (Only Logged-In Users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    if (!isset($_SESSION['user_id'])) {
        send_response("error", "You must be logged in to vote!");
    }

    $user_id = $_SESSION['user_id'];
    $candidate_id = intval($_POST['candidate']);

    // ✅ Check if the user has already voted
    $check_vote_sql = "SELECT id FROM votes WHERE user_id = ?";
    $check_vote_stmt = mysqli_prepare($conn, $check_vote_sql);
    mysqli_stmt_bind_param($check_vote_stmt, "i", $user_id);
    mysqli_stmt_execute($check_vote_stmt);
    mysqli_stmt_store_result($check_vote_stmt);

    if (mysqli_stmt_num_rows($check_vote_stmt) > 0) {
        send_response("error", "You have already voted!");
    }

    // ✅ Insert vote record
    $insert_vote_sql = "INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)";
    $insert_vote_stmt = mysqli_prepare($conn, $insert_vote_sql);
    mysqli_stmt_bind_param($insert_vote_stmt, "ii", $user_id, $candidate_id);

    if (mysqli_stmt_execute($insert_vote_stmt)) {
        // ✅ Increase vote count for candidate
        $update_candidate_sql = "UPDATE candidates SET votes = votes + 1 WHERE id = ?";
        $update_candidate_stmt = mysqli_prepare($conn, $update_candidate_sql);
        mysqli_stmt_bind_param($update_candidate_stmt, "i", $candidate_id);
        mysqli_stmt_execute($update_candidate_stmt);
        send_response("success", "Vote recorded successfully!");
    } else {
        send_response("error", "Error: " . mysqli_error($conn));
    }
}

// ✅ GET ELECTION RESULTS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_results'])) {
    $sql = "SELECT * FROM candidates ORDER BY votes DESC";
    $result = mysqli_query($conn, $sql);
    $candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($candidates);
    exit;
}

mysqli_close($conn);

?>