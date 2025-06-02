<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "ufjzzoomqznvk", "tm2savfugtmk", "db5cmr2gum8ye2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
