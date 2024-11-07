<?php

$host = 'sql12.freesqldatabase.com';
$dbname = 'sql12743168';
$username = 'sql12743168';
$password = 'TnRn5yDYvX'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
