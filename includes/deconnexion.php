<?php

// Démarre ou reprend la session de l'utilisateur.
session_start();

// Supprime toutes les variables actuellement stockées dans la session.
$_SESSION = [];

// Vérifie si la session est gérée avec un cookie.
if (ini_get('session.use_cookies')) {

    // Récupère les paramètres du cookie de session.
    $parametres = session_get_cookie_params();

    // Expire le cookie afin de le supprimer du navigateur.
    setcookie(
        session_name(),          // Nom du cookie de session.
        '',                      // Valeur vide.
        time() - 42000,          // Date d'expiration située dans le passé.
        $parametres['path'],     // Chemin concerné par le cookie.
        $parametres['domain'],   // Domaine concerné.
        $parametres['secure'],   // Cookie envoyé uniquement via HTTPS si activé.
        $parametres['httponly']  // Empêche JavaScript d'accéder au cookie.
    );
}

// Détruit définitivement la session côté serveur.
session_destroy();

// Redirige l'utilisateur vers la page d'accueil.
header('Location: ../accueil.php');

// Interrompt le script après la redirection.
exit;