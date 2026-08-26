<?php
session_start();

require_once 'config/database.php';

if (!isset($_SESSION['IdUtilisateur'])) {
    header('Location: includes/connexion.php');
    exit;
}

$idUtilisateur = (int) $_SESSION['IdUtilisateur'];
$idContenu = filter_input(INPUT_POST, 'id_contenu', FILTER_VALIDATE_INT);

if (!$idContenu) {
    header('Location: accueil.php');
    exit;
}

try {
    // Évite d'ajouter deux fois le même contenu
    $verification = $pdo->prepare("
        SELECT COUNT(*)
        FROM ma_liste
        WHERE IdUtilisateur = :utilisateur
        AND IdContenu = :contenu
    ");

    $verification->execute([
        ':utilisateur' => $idUtilisateur,
        ':contenu' => $idContenu
    ]);

    if ($verification->fetchColumn() == 0) {
        $ajout = $pdo->prepare("
            INSERT INTO ma_liste (IdUtilisateur, IdContenu)
            VALUES (:utilisateur, :contenu)
        ");

        $ajout->execute([
            ':utilisateur' => $idUtilisateur,
            ':contenu' => $idContenu
        ]);
    }

    header('Location: ma-liste.php');
    exit;

} catch (PDOException $e) {
    die('Erreur lors de l’ajout : ' . $e->getMessage());
}