<?php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];
$yourNumber = $_SESSION["number"] ?? 'Unknown';
$yourName = $_SESSION["name"] ?? 'User';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["contact_number"])) {
    $contact_number = $conn->real_escape_string(trim($_POST["contact_number"]));

    if ($contact_number === $yourNumber) {
        header("Location: home.php?status=self");
        exit();
    }

    $check = $conn->query("SELECT id FROM users WHERE unique_number='$contact_number'");
    if ($check && $check->num_rows === 1) {
        $contact_id = $check->fetch_assoc()["id"];
        $exists = $conn->query("SELECT * FROM contacts WHERE user_id=$userId AND contact_id=$contact_id");
        if ($exists && $exists->num_rows === 0) {
            $conn->query("INSERT INTO contacts (user_id, contact_id) VALUES ($userId, $contact_id)");
            header("Location: home.php?status=added");
            exit();
        } else {
            header("Location: home.php?status=exists");
            exit();
        }
    } else {
        header("Location: home.php?status=notfound");
        exit();
    }
}

$contacts = $conn->query("SELECT users.* FROM users
    JOIN contacts ON contacts.contact_id = users.id
    WHERE contacts.user_id = $userId");
?>

<!DOCTYPE html>
<html>
<head>
    <title>WhatsApp Clone</title>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; font-family: Arial; display: flex; height: 100vh; }
        .sidebar {
            width: 30%; background: #075e54; color: white; padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .main {
            width: 70%; background: #ece5dd; padding: 20px;
        }
        h2 { margin-bottom: 5px; }
        .logout {
            float: right; color: white; font-size: 14px; text-decoration: none;
        }
        .contact {
            background: #128c7e; padding: 10px;
            border-radius: 5px; margin: 10px 0;
            color: white; text-decoration: none;
            display: block;
            transition: background 0.3s;
        }
        .contact:hover {
            background: #25d366;
        }
        form input, form button {
            width: 100%; padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: none;
        }
        form button {
            background: #25d366;
            color: white;
            font-weight: bold;
        }
        .status-msg {
            margin-bottom: 15px;
            padding: 10px;
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><?= htmlspecialchars($yourName) ?> <a class="logout" href="logout.php">Logout</a></h2>
        <p>Your Number: <strong><?= htmlspecialchars($yourNumber) ?></strong></p>

        <form method="POST">
            <input type="text" name="contact_number" placeholder="Add contact (+786-xxxx)" required />
            <button type="submit">Add Contact</button>
        </form>

        <h3>Your Contacts:</h3>
        <?php if ($contacts && $contacts->num_rows > 0): ?>
            <?php while ($row = $contacts->fetch_assoc()): ?>
                <a class="contact" href="chat.php?user=<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['unique_number']) ?>)
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No contacts added yet.</p>
        <?php endif; ?>
    </div>

    <div class="main">
        <h2>Select a contact to start chatting</h2>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        if (params.has("status")) {
            let msg = "";
            switch (params.get("status")) {
                case "added":
                    msg = "Contact added successfully.";
                    break;
                case "exists":
                    msg = "This contact already exists.";
                    break;
                case "self":
                    msg = "You cannot add yourself.";
                    break;
                case "notfound":
                    msg = "Contact not found.";
                    break;
            }
            if (msg) {
                alert(msg);
                window.history.replaceState({}, document.title, "home.php");
            }
        }
    </script>
</body>
</html>
