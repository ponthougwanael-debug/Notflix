<?php
// On importe les identifiants secrets
require_once 'database_1.php'; // Ajustez le chemin (ex: 'config/database_1.php') si besoin

try {
    // On utilise les variables qui viennent de database_1.php
    // Attention ici, j'ai corrigé $dbname en $db_name !
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e) {
    die("Erreur de connexion : ".$e->getMessage());
}
?>