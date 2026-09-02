<?php
// Vérifie si une session est déjà démarrée.
// Si ce n'est pas le cas, démarre une nouvelle session.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupère le nom du fichier PHP actuellement exécuté,
// puis supprime son extension ".php".
$page_actuelle = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Définit l'encodage des caractères en UTF-8. -->
    <meta charset="UTF-8">

    <!-- Adapte l'affichage du site aux écrans mobiles. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Définit le titre affiché dans l'onglet du navigateur. -->
    <title>Notflix</title>

    <!-- Charge la feuille de style principale. -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Charge les styles spécifiques à la recherche. -->
    <link rel="stylesheet" href="css/recherche.css">

    <?php
    // Vérifie si une feuille CSS portant le nom de la page existe.
    if (file_exists(__DIR__ . "/../css/$page_actuelle.css")):
    ?>
        <!-- Charge automatiquement le CSS propre à la page actuelle. -->
        <link rel="stylesheet" href="css/<?= $page_actuelle ?>.css">
    <?php endif; ?>
</head>

<body>

<!-- Barre principale de navigation du site. -->
<header class="barre-navigation">

    <!-- Conteneur du logo. -->
    <div class="logo">

        <!-- Lien permettant de retourner à la page d'accueil. -->
        <a href="accueil.php">NOTFLIX</a>
    </div>

    <!-- Menu de navigation. -->
    <nav>

        <!-- Lien vers la page d'accueil. -->
        <a href="accueil.php">Accueil</a>

        <!-- Lien vers le catalogue des contenus. -->
        <a href="contenu.php">Catalogue</a>

        <?php
        // Vérifie si un utilisateur est connecté.
        if (isset($_SESSION['IdUtilisateur'])):
        ?>

            <!-- Affiche le lien vers la liste personnelle. -->
            <a href="/notflix/ma-liste.php">Ma liste</a>

        <?php endif; ?>

        <?php
        // Vérifie à nouveau si un utilisateur est connecté.
        if (isset($_SESSION['IdUtilisateur'])):
        ?>

            <!-- Affiche un message de bienvenue avec le prénom de l'utilisateur. -->
            <span class="bienvenue">
                Bonjour <?= htmlspecialchars($_SESSION['Prenom']) ?>
            </span>

            <!-- Affiche le lien de déconnexion. -->
            <a class="bouton-deconnexion" href="includes/deconnexion.php">
                Se déconnecter
            </a>

        <?php else: ?>

            <!-- Affiche le lien d'inscription si personne n'est connecté. -->
            <a href="inscription.php">S'inscrire</a>

            <!-- Affiche le lien de connexion si personne n'est connecté. -->
            <a href="includes/connexion.php">Se connecter</a>

        <?php endif; ?>

    </nav>

    <style>
        /*
         * Empêche les images d'être déplacées par glisser-déposer.
         * -webkit-user-drag fonctionne principalement avec Chrome et Safari.
         * user-select empêche la sélection de l'image.
         */
        img {
            -webkit-user-drag: none;
            user-select: none;
            -webkit-user-select: none;
        }
    </style>

    <script>
        // Attend que toute la page HTML soit chargée.
        document.addEventListener('DOMContentLoaded', function () {

            // Sélectionne toutes les images présentes sur la page.
            document.querySelectorAll('img').forEach(function (image) {

                // Désactive le glisser-déposer directement dans le HTML.
                image.setAttribute('draggable', 'false');

                // Intercepte la tentative de glisser-déposer.
                image.addEventListener('dragstart', function (event) {
                    event.preventDefault();
                });

                // Désactive le menu contextuel lors d'un clic droit sur l'image.
                image.addEventListener('contextmenu', function (event) {
                    event.preventDefault();
                });
            });
        });
    </script>

</header>