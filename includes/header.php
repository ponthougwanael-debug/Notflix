<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_actuelle = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notflix</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/recherche.css">
    <link rel="stylesheet" href="../css/connexion.css"

    <?php if (file_exists(__DIR__ . "/../css/$page_actuelle.css")): ?>
        <link rel="stylesheet" href="css/<?= $page_actuelle ?>.css">
    <?php endif; ?>
</head>

<body>

<header class="barre-navigation">

    <div class="logo">
        <a href="accueil.php">NOTFLIX</a>
    </div>

    <nav>
        <a href="accueil.php">Accueil</a>
        <a href="contenu.php">Catalogue</a>
        <?php
        // Affiche le lien "Ma liste" uniquement lorsqu'un utilisateur est connecte.
        if (isset($_SESSION['IdUtilisateur'])):
        ?>
        <a href="/notflix/ma-liste.php">Ma liste</a>
        <?php
        // Fin du bloc conditionnel qui controle l'affichage du lien "Ma liste".
        endif;
        ?>

        <?php if (isset($_SESSION['IdUtilisateur'])): ?>

            <span class="bienvenue">
                Bonjour <?= htmlspecialchars($_SESSION['Prenom']) ?>
            </span>

            <a class="bouton-deconnexion" href="includes/deconnexion.php">
                Se déconnecter
            </a>

        <?php else: ?>

            <a href="inscription.php">S'inscrire</a>
            <a href="connexion.php">Se connecter</a>

        <?php endif; ?>
    </nav>

</header>