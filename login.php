<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST["name"]);
    $password = $_POST["password"];

    $user = $conn->query("SELECT * FROM users WHERE name='$name'");
    if ($user && $user->num_rows === 1) {
        $data = $user->fetch_assoc();

        // Verify password
        if (password_verify($password, $data["password"])) {
            $_SESSION["user_id"] = $data["id"];
            $_SESSION["number"] = $data["unique_number"];
            $_SESSION["name"] = $data["name"];
            echo "<script>window.location.href='home.php';</script>";
        } else {
            $error = "Wrong password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - WhatsApp Clone</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background: #dff0d8; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px gray; width: 300px; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #25d366; color: white; font-weight: bold; }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Your Name" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>
        <p><a href="signup.php">Create new account</a></p>
    </div>
</body>
</html>
