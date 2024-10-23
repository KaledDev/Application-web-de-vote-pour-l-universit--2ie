<?php

$host = 'localhost';
$dbname = 'vote2ie';
$username = 'root';      
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Affiche un message d'erreur en cas de problème de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
