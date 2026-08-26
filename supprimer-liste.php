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
    header('Location: ma-liste.php');
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM ma_liste
    WHERE IdUtilisateur = :utilisateur
    AND IdContenu = :contenu
");

$stmt->execute([
    ':utilisateur' => $idUtilisateur,
    ':contenu' => $idContenu
]);

header('Location: ma-liste.php');
exit;