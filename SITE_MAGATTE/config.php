<?php
// config.php

// Définir les constantes de configuration
define("DB_HOST", "localhost");
define("DB_NAME", "gueyeh25techinfo_magatte_site");
define("DB_USER", "gueyeh25techinfo_magatte");
define("DB_PASS", "B@yeniass");  // 
define("DB_CHARSET", "utf8mb4");

// Construction du DSN
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage()); 
    die("Erreur de connexion à la base de données."); 
}
?>
