<?php
// Démarre la session afin d'accéder aux informations de l'utilisateur connecté.
session_start();

// Charge la connexion à la base de données.
require_once 'config/database.php';

// Vérifie si l'utilisateur est connecté.
if (!isset($_SESSION['IdUtilisateur'])) {
    // Redirige vers la page de connexion si aucune session n'est trouvée.
    header('Location: includes/connexion.php');
    exit;
}

// Récupère l'identifiant de l'utilisateur connecté et le convertit en entier.
$idUtilisateur = (int) $_SESSION['IdUtilisateur'];

// Récupère et valide l'identifiant du contenu envoyé par le formulaire.
$idContenu = filter_input(
    INPUT_POST,
    'id_contenu',
    FILTER_VALIDATE_INT
);

// Vérifie que l'identifiant du contenu est valide.
if (!$idContenu) {
    // Redirige vers la page d'accueil si l'identifiant est invalide.
    header('Location: accueil.php');
    exit;
}

try {
    // Prépare une requête pour vérifier si le contenu
    // existe déjà dans la liste de l'utilisateur.
    $verification = $pdo->prepare("
        SELECT COUNT(*)
        FROM ma_liste
        WHERE IdUtilisateur = :utilisateur
        AND IdContenu = :contenu
    ");

    // Exécute la requête avec les identifiants de l'utilisateur
    // et du contenu.
    $verification->execute([
        ':utilisateur' => $idUtilisateur,
        ':contenu' => $idContenu
    ]);

    // Ajoute le contenu uniquement s'il n'est pas déjà présent.
    if ($verification->fetchColumn() == 0) {
        // Prépare la requête d'insertion dans la liste personnelle.
        $ajout = $pdo->prepare("
            INSERT INTO ma_liste (IdUtilisateur, IdContenu)
            VALUES (:utilisateur, :contenu)
        ");

        // Exécute l'insertion avec les identifiants correspondants.
        $ajout->execute([
            ':utilisateur' => $idUtilisateur,
            ':contenu' => $idContenu
        ]);
    }

    // Redirige l'utilisateur vers sa liste après l'ajout.
    header('Location: ma-liste.php');
    exit;

} catch (PDOException $e) {
    // Affiche un message si une erreur liée à la base de données survient.
    die('Erreur lors de l’ajout : ' . $e->getMessage());
}