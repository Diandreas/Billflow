<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS billflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "Base de données 'billflow' créée ou existante.\n";
} catch (PDOException $e) {
    die("Erreur de connexion à MySQL: " . $e->getMessage() . "\n");
}

?> 