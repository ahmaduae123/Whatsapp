<?php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];
$receiver_id = $_GET["user"];

// ✅ Fixed: use 'name' instead of 'username'
$receiver = $conn->query("SELECT name FROM users WHERE id=$receiver_id");
if (!$receiver || $receiver->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='home.php';</script>";
    exit();
}
$receiver_name = $receiver->fetch_assoc()["name"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with <?= htmlspecialchars($receiver_name) ?></title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background: #ece5dd; padding: 20px; }
        .chat-box {
            background: white; height: 400px; overflow-y: auto;
            padding: 10px; margin-bottom: 10px;
            border: 1px solid #ccc; border-radius: 5px;
        }
        .message { margin: 10px; }
        .sent { text-align: right; color: green; }
        .received { text-align: left; color: blue; }
        form { display: flex; gap: 10px; }
        input[type="text"] {
            flex: 1; padding: 10px;
        }
        button {
            padding: 10px 20px;
            background: #25d366;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Chat with <?= htmlspecialchars($receiver_name) ?> <a href="home.php" style="float:right; font-size:14px;">Back</a></h2>

    <div class="chat-box" id="chat-box"></div>

    <form id="msgForm">
        <input type="hidden" id="receiver_id" value="<?= $receiver_id ?>">
        <input type="text" id="message" placeholder="Type a message..." required />
        <button type="submit">Send</button>
    </form>

    <script>
        function loadMessages() {
            let x = new XMLHttpRequest();
            x.open("GET", "getMessages.php?receiver_id=" + <?= $receiver_id ?>, true);
            x.onload = function () {
                document.getElementById("chat-box").innerHTML = this.responseText;
                document.getElementById("chat-box").scrollTop = document.getElementById("chat-box").scrollHeight;
            };
            x.send();
        }

        document.getElementById("msgForm").onsubmit = function (e) {
            e.preventDefault();
            let msg = document.getElementById("message").value;
            let rec = document.getElementById("receiver_id").value;
            let x = new XMLHttpRequest();
            x.open("POST", "sendMessage.php", true);
            x.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            x.onload = function () {
                document.getElementById("message").value = "";
                loadMessages();
            };
            x.send("message=" + encodeURIComponent(msg) + "&receiver_id=" + rec);
        };

        setInterval(loadMessages, 1000);
        loadMessages();
    </script>
</body>
</html>
