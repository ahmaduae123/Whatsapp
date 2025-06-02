<?php
session_start();
include("db.php");

$sender = $_SESSION["user_id"];
$receiver = $_GET["receiver_id"];

$sql = "SELECT * FROM messages 
        WHERE (sender_id=$sender AND receiver_id=$receiver) 
           OR (sender_id=$receiver AND receiver_id=$sender) 
        ORDER BY created_at ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $class = ($row["sender_id"] == $sender) ? "sent" : "received";
    echo "<div class='message $class'>" . htmlspecialchars($row["message"]) . "<br><small>" . $row["created_at"] . "</small></div>";
}
?>
