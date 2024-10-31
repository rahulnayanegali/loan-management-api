<?php
$host = getenv('DB_HOST');
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to the database.\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables in the database:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Tables_in_' . $db] . "\n";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}