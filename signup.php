<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST["name"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Generate unique number like +786-1001
    $lastUser = $conn->query("SELECT unique_number FROM users ORDER BY id DESC LIMIT 1");
    $lastNumber = 1000;
    if ($lastUser->num_rows > 0) {
        $last = $lastUser->fetch_assoc()["unique_number"];
        $lastNumber = (int)explode("-", $last)[1];
    }
    $newNumber = "+786-" . ($lastNumber + 1);

    // Insert into database
    $insert = $conn->query("INSERT INTO users (name, password, unique_number) VALUES ('$name', '$password', '$newNumber')");
    if ($insert) {
        echo "<script>alert('Signup successful. Your number is $newNumber'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Signup failed. Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup - WhatsApp Clone</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px gray; width: 300px; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #25d366; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Sign Up</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Your Name" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Sign Up</button>
        </form>
        <p><a href="login.php">Already have an account? Login</a></p>
    </div>
</body>
</html>
