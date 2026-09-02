<?php

// Inclut le fichier contenant les identifiants de connexion.
// require_once empêche l'inclusion du fichier plusieurs fois.
require_once 'database_1.php';

// Tente d'établir une connexion avec la base de données.
try {

    // Crée une connexion PDO à la base de données MySQL.
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password
    );

    // Configure PDO pour transformer les erreurs SQL en exceptions.
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

// Capture les erreurs de connexion.
} catch (PDOException $e) {

    // Enregistre le détail de l'erreur dans les journaux du serveur.
    error_log($e->getMessage());

    // Affiche un message général sans révéler d'informations sensibles.
    die("Impossible de se connecter à la base de données.");
}

?>