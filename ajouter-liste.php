<?php
// ==========================================
// DÉMARRAGE DE LA SESSION
// ==========================================
// Nécessaire pour accéder à $_SESSION (utilisateur connecté)
session_start();

// Connexion à la base de données ($pdo)
require_once 'config/database.php';

// ==========================================
// VÉRIFICATION DE LA CONNEXION UTILISATEUR
// ==========================================
// Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['IdUtilisateur'])) {
    header('Location: includes/connexion.php');
    exit;
}

// Récupère l'ID de l'utilisateur connecté, casté en entier par sécurité
$idUtilisateur = (int) $_SESSION['IdUtilisateur'];

// ==========================================
// RÉCUPÉRATION ET VALIDATION DE L'ID DU CONTENU
// ==========================================
// filter_input valide que la donnée POST est bien un entier
// Renvoie false si invalide, null si absent
$idContenu = filter_input(INPUT_POST, 'id_contenu', FILTER_VALIDATE_INT);

// Si l'ID est invalide ou absent, on annule l'opération et on redirige
if (!$idContenu) {
    header('Location: accueil.php');
    exit;
}

try {
    // ==========================================
    // VÉRIFICATION : LE CONTENU EXISTE-T-IL VRAIMENT ?
    // ==========================================
    // Évite d'ajouter un IdContenu qui n'existe pas dans la table Contenu
    $verifContenu = $pdo->prepare("
        SELECT COUNT(*)
        FROM Contenu
        WHERE IdContenu = :contenu
    ");

    $verifContenu->execute([':contenu' => $idContenu]);

    // Si le contenu n'existe pas, on arrête ici et on redirige proprement
    if ($verifContenu->fetchColumn() == 0) {
        header('Location: accueil.php');
        exit;
    }

    // ==========================================
    // VÉRIFICATION : LE CONTENU EST-IL DÉJÀ DANS LA LISTE ?
    // ==========================================
    // Évite d'ajouter deux fois le même contenu pour le même utilisateur
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

    // fetchColumn() récupère directement la valeur du COMPTAGE (un nombre)
    // Si 0, le contenu n'est pas encore dans la liste : on peut l'ajouter
    if ($verification->fetchColumn() == 0) {
        // ==========================================
        // AJOUT DU CONTENU À LA LISTE DE L'UTILISATEUR
        // ==========================================
        $ajout = $pdo->prepare("
            INSERT INTO ma_liste (IdUtilisateur, IdContenu)
            VALUES (:utilisateur, :contenu)
        ");

        $ajout->execute([
            ':utilisateur' => $idUtilisateur,
            ':contenu' => $idContenu
        ]);
    }

    // ==========================================
    // REDIRECTION VERS "MA LISTE" APRÈS TRAITEMENT
    // ==========================================
    header('Location: ma-liste.php');
    exit;

} catch (PDOException $e) {
    // ==========================================
    // GESTION DES ERREURS DE BASE DE DONNÉES
    // ==========================================
    // En production, on évite d'afficher $e->getMessage() à l'utilisateur
    // (fuite d'informations sensibles sur la structure de la BDD)
    // On peut journaliser l'erreur dans un fichier log pour le débogage :
    error_log('Erreur ajout ma_liste : ' . $e->getMessage());

    // Redirection propre vers une page d'erreur ou l'accueil
    header('Location: accueil.php?erreur=ajout');
    exit;
}