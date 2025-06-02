<?php
session_start();
include("db.php");

$sender = $_SESSION["user_id"];
$receiver = $_POST["receiver_id"];
$message = $conn->real_escape_string($_POST["message"]);

if (!empty($message)) {
    $conn->query("INSERT INTO messages (sender_id, receiver_id, message) VALUES ($sender, $receiver, '$message')");
}
?>
