<?php
/**
 * Supprime un contenu de la liste personnelle de l'utilisateur connecté.
 *
 * Le formulaire de ma-liste.php doit envoyer l'identifiant dans le champ
 * POST « id_contenu ». Après le traitement (ou après une requête invalide),
 * l'utilisateur est renvoyé vers ma-liste.php.
 */

declare(strict_types=1);

// Démarre la session afin de pouvoir lire l'utilisateur authentifié.
// La configuration des cookies de session (Secure, HttpOnly, SameSite) doit
// idéalement être définie dans la configuration globale, avant session_start().
session_start();

/**
 * Redirige vers la page de liste puis arrête immédiatement le script.
 * Le type de retour "never" indique explicitement que cette fonction
 * ne retourne jamais (elle termine toujours le script avec exit).
 */
function redirigerVersMaListe(): never
{
    header('Location: ma-liste.php');
    exit;
}

// Cette action modifie des données : elle doit être appelée exclusivement en POST.
// Un accès direct en GET ne doit jamais déclencher une suppression.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirigerVersMaListe();
}

// Vérifie la présence et la validité de l'identifiant stocké côté serveur.
// Même si cette valeur vient de la session, on refuse toute valeur absente,
// non entière ou non positive plutôt que de la convertir implicitement en 0.
$idUtilisateur = filter_var(
    $_SESSION['IdUtilisateur'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idUtilisateur === false) {
    // Une session absente ou incohérente ne donne pas accès à l'action.
    header('Location: includes/connexion.php');
    exit;
}

// Valide strictement l'identifiant fourni par le navigateur. Une valeur
// invalide (champ absent, texte, décimale ou identifiant <= 0) est ignorée.
$idContenu = filter_input(
    INPUT_POST,
    'id_contenu',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idContenu === false || $idContenu === null) {
    redirigerVersMaListe();
}

// Utilise un chemin fondé sur le dossier de ce fichier : le require reste
// fiable même si le répertoire courant PHP change.
require_once __DIR__ . '/config/database.php';

try {
    // Les deux critères sont indispensables : l'identifiant de contenu ne
    // suffit pas. Ainsi, un utilisateur ne peut supprimer que sa propre ligne
    // dans ma_liste, même s'il modifie la valeur du champ hidden.
    $stmt = $pdo->prepare(
        'DELETE FROM ma_liste
         WHERE IdUtilisateur = :utilisateur
           AND IdContenu = :contenu'
    );

    // Les paramètres sont liés explicitement comme entiers : aucune
    // concaténation de données utilisateur dans la requête SQL (protection
    // contre l'injection SQL).
    $stmt->bindValue(':utilisateur', $idUtilisateur, PDO::PARAM_INT);
    $stmt->bindValue(':contenu', $idContenu, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $exception) {
    // Ne montre pas les détails SQL à l'utilisateur (ils peuvent contenir des
    // informations sensibles). Les détails restent disponibles dans les logs
    // serveur pour le diagnostic ; la redirection évite une page d'erreur brute.
    error_log('Erreur lors de la suppression dans ma_liste : ' . $exception->getMessage());
}

// La suppression est terminée (ou l'erreur a été journalisée) : retour à la liste.
redirigerVersMaListe();