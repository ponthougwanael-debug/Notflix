<?php

// Démarre la session pour mémoriser les informations de l'utilisateur connecté.
session_start();

// Charge la connexion à la base de données.
require_once __DIR__ . '/../config/database.php';

// Initialise la variable qui contiendra les erreurs.
$erreur = '';

// Vérifie si le formulaire a été envoyé.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère l'adresse e-mail et supprime les espaces inutiles.
    $email = trim($_POST['email'] ?? '');

    // Récupère le mot de passe saisi.
    $motdepasse = $_POST['motdepasse'] ?? '';

    // Vérifie si un champ est vide.
    if ($email === '' || $motdepasse === '') {

        // Affiche un message d'erreur.
        $erreur = 'Veuillez remplir tous les champs.';

    } else {

        // Prépare une requête pour rechercher l'utilisateur par son e-mail.
        $stmt = $pdo->prepare(
            'SELECT * FROM UTILISATEUR WHERE Email = ?'
        );

        // Exécute la requête avec l'e-mail saisi.
        $stmt->execute([$email]);

        // Récupère les informations de l'utilisateur.
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifie que l'utilisateur existe et que le mot de passe est correct.
        if (
            $utilisateur &&
            password_verify($motdepasse, $utilisateur['MotDePasse'])
        ) {

            // Enregistre l'identifiant de l'utilisateur dans la session.
            $_SESSION['IdUtilisateur'] = $utilisateur['IdUtilisateur'];

            // Enregistre le prénom de l'utilisateur dans la session.
            $_SESSION['Prenom'] = $utilisateur['Prenom'];

            // Redirige l'utilisateur vers la page du catalogue.
            header('Location: ../contenu.php');

            // Arrête le script après la redirection.
            exit;
        }

        // Message affiché si l'e-mail ou le mot de passe est incorrect.
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Définit l'encodage des caractères. -->
    <meta charset="UTF-8">

    <!-- Rend la page adaptée aux téléphones et aux tablettes. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Définit le titre de la page dans l'onglet du navigateur. -->
    <title>Connexion - Notflix</title>

    <!-- Charge la feuille de style générale. -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Charge la feuille de style de la connexion. -->
    <link rel="stylesheet" href="../css/connexion.css">
</head>

<body>

<!-- Conteneur principal de la page de connexion. -->
<div class="conteneur-connexion">

    <!-- Carte contenant le formulaire. -->
    <div class="carte-connexion">

        <!-- Titre de la page. -->
        <h2>Connexion</h2>

        <!-- Formulaire envoyé avec la méthode POST. -->
        <form method="POST" action="connexion.php">

            <!-- Champ destiné à l'adresse e-mail. -->
            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >

            <!-- Champ destiné au mot de passe. -->
            <input
                type="password"
                name="motdepasse"
                placeholder="Mot de passe"
                required
            >

            <!-- Bouton d'envoi du formulaire. -->
            <button type="submit">
                Se connecter
            </button>

        </form>

        <!-- Affiche le message d'erreur uniquement s'il existe. -->
        <?php if ($erreur !== ''): ?>

            <!-- Paragraphe contenant le message d'erreur. -->
            <p class="message-erreur">

                <!-- Affiche l'erreur de manière sécurisée. -->
                <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>

            </p>

        <?php endif; ?>

        <!-- Texte destiné aux personnes qui n'ont pas encore de compte. -->
        <p class="lien-inscription">
            Pas encore de compte ?
            <!-- Lien vers la page d'inscription. -->
            <a href="../inscription.php">S'inscrire</a>
        </p>
        <p class="lien-connexion">
            Revenir vers la page d'accueil
            <a href="../accueil.php">Clique ici</a>
        </p>

    </div>
</div>

</body>
</html>