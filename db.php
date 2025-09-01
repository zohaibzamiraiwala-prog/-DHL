<?php
// db.php - Database connection file
$servername = "localhost"; // Assuming local server, change if needed
$username = "unkuodtm3putf";
$password = "htk2glkxl4n4";
$dbname = "dbjujixigfeje9";
 
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
