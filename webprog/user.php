<?php
// Force to show errors in any PHP installation.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "db_attendancemanagement");

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Check if username and password are sent
if (!isset($_POST['Employee_NAME']) || !isset($_POST['Employee_ID'])) {
    echo json_encode(["success" => false, "message" => "Missing username or password"]);
    exit();
}

$Employee_NAME = trim($_POST['Employee_NAME']);
$Employee_ID = trim($_POST['Employee_ID']);
$Employee_TIME_IN = trim($_POST['Employee_TIME_IN']);

// Check if employee exists
$sql = "SELECT Employee_ID, Employee_NAME FROM employee_details WHERE Employee_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $Employee_ID);
$stmt->execute();
$result = $stmt->get_result();

$user_found = false;

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (strcmp($Employee_ID, $user['Employee_ID']) == 0) {
        $user_found = true;
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

if ($user_found){
    // Convert time string to integer first
    $time = explode(':', $Employee_TIME_IN);
    $hour = (int) $time[0];
    $minute = (int) $time[1];

    $stmt->close();
    $sql = "INSERT INTO attendance (Employee_ID, Employee_TIME_IN) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $Employee_ID, $Employee_TIME_IN);
    $stmt->execute();

    // The employee is considered to be late if it's beyond 9:15 AM
    if ($hour > 9 || ($hour == 9 && $minute > 15)) {
        echo json_encode(["success" => true, "message" => "You are late!"]);
    } else {
        echo json_encode(["success" => true]);
    }
}
$stmt->close();
$conn->close();

?>