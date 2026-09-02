<?php
// Charge le fichier contenant la connexion à la base de données.
// require_once évite de charger plusieurs fois le même fichier.
require_once 'config/database.php';

// Charge l'en-tête commun du site, par exemple le menu et le logo.
include 'includes/header.php';


// --------------------------------------------------
// RÉCUPÉRATION DU CONTENU MIS EN AVANT
// --------------------------------------------------

// Exécute une requête SQL pour récupérer un contenu au hasard.
// SELECT * récupère toutes les colonnes.
// ORDER BY RAND() mélange les contenus.
// LIMIT 1 demande un seul résultat.
$requeteUne = $pdo->query(
    "SELECT * FROM Contenu ORDER BY RAND() LIMIT 1"
);

// Récupère le résultat de la requête.
// Le contenu est stocké dans un tableau associatif.
$contenuUne = $requeteUne->fetch();


// --------------------------------------------------
// RÉCUPÉRATION DES FILMS
// --------------------------------------------------

// Recherche les contenus dont le type est "film".
// ORDER BY Annee DESC classe les films du plus récent au plus ancien.
// LIMIT 3 : limite le résultat à trois films.
$requeteFilms = $pdo->query(
    "SELECT *
     FROM Contenu
     WHERE Type = 'film'
     ORDER BY Annee DESC
     LIMIT 3"
);

// Récupère les trois films sous forme de tableau.
$films = $requeteFilms->fetchAll();


// --------------------------------------------------
// RÉCUPÉRATION DES SÉRIES
// --------------------------------------------------

// Recherche les contenus dont le type est "serie".
// Les séries sont également classées de la plus récente à la plus ancienne.
$requeteSeries = $pdo->query(
    "SELECT *
     FROM Contenu
     WHERE Type = 'serie'
     ORDER BY Annee DESC
     LIMIT 3"
);

// Récupère toutes les séries trouvées.
$series = $requeteSeries->fetchAll();
?>

<!--
    SECTION DU CONTENU MIS EN AVANT
    Cette partie affiche un contenu choisi aléatoirement.
-->
<section class="a-la-une">

    <!--
        Affiche l'affiche du contenu.
        htmlspecialchars() protège la page contre l'injection de code HTML.
    -->
    <img
        src="Images/<?= htmlspecialchars($contenuUne['Affiche']) ?>"
        alt="<?= htmlspecialchars($contenuUne['Titre']) ?>"
    >

    <!-- Contient le titre et l'année du contenu mis en avant. -->
    <div class="infos-une">

        <!-- Affiche le titre du contenu. -->
        <h2>
            <?= htmlspecialchars($contenuUne['Titre']) ?>
        </h2>

        <!-- Affiche l'année de sortie. -->
        <p>
            <?= htmlspecialchars($contenuUne['Annee']) ?>
        </p>

    </div>
</section>


<!--
    SECTION DES FILMS
    Cette section affiche les trois films les plus récents.
-->
<section class="decouverte">

    <!-- Titre visible de la section. -->
    <h2>Films à découvrir</h2>

    <!-- Conteneur qui regroupe toutes les cartes de films. -->
    <div class="liste-cartes">

        <!--
            Parcourt le tableau $films.
            À chaque répétition, un film est placé dans la variable $film.
        -->
        <?php foreach ($films as $film): ?>

            <!-- Carte représentant un film. -->
            <div class="carte">

                <!-- Conteneur des images du film. -->
                <div class="carte-image">

                    <!--
                        Image utilisée comme arrière-plan flou.
                        La classe CSS "fond-flou" applique probablement un effet de flou.
                        alt="" signifie que cette image est décorative.
                    -->
                    <img
                        src="Images/<?= htmlspecialchars($film['Affiche']) ?>"
                        class="fond-flou"
                        alt=""
                    >

                    <!--
                        Image principale affichée nettement.
                        Le texte alternatif contient le titre du film.
                    -->
                    <img
                        src="Images/<?= htmlspecialchars($film['Affiche']) ?>"
                        class="affiche-nette"
                        alt="<?= htmlspecialchars($film['Titre']) ?>"
                    >

                </div>

                <!-- Affiche le titre du film. -->
                <h3>
                    <?= htmlspecialchars($film['Titre']) ?>
                </h3>

                <!-- Affiche l'année de sortie du film. -->
                <p>
                    <?= htmlspecialchars($film['Annee']) ?>
                </p>

            </div>

        <!-- Fin de la boucle qui parcourt les films. -->
        <?php endforeach; ?>

    </div>
</section>


<!--
    SECTION DES SÉRIES
    Cette section fonctionne comme celle des films,
    mais elle affiche les séries.
-->
<section class="decouverte">

    <!-- Titre visible de la section. -->
    <h2>Séries à découvrir</h2>

    <!-- Conteneur qui regroupe les cartes des séries. -->
    <div class="liste-cartes">

        <!--
            Parcourt le tableau $series.
            À chaque répétition, une série est placée dans $serie.
        -->
        <?php foreach ($series as $serie): ?>

            <!-- Carte représentant une série. -->
            <div class="carte">

                <!-- Conteneur des images de la série. -->
                <div class="carte-image">

                    <!-- Image floutée utilisée comme arrière-plan. -->
                    <img
                        src="Images/<?= htmlspecialchars($serie['Affiche']) ?>"
                        class="fond-flou"
                        alt=""
                    >

                    <!-- Image nette de la série. -->
                    <img
                        src="Images/<?= htmlspecialchars($serie['Affiche']) ?>"
                        class="affiche-nette"
                        alt="<?= htmlspecialchars($serie['Titre']) ?>"
                    >

                </div>

                <!-- Affiche le titre de la série. -->
                <h3>
                    <?= htmlspecialchars($serie['Titre']) ?>
                </h3>

                <!-- Affiche l'année de sortie de la série. -->
                <p>
                    <?= htmlspecialchars($serie['Annee']) ?>
                </p>

            </div>

        <!-- Fin de la boucle qui parcourt les séries. -->
        <?php endforeach; ?>

    </div>
</section>


<!--
    Bouton permettant d'accéder à tous les films et séries.
-->
<div class="voir-plus">

    <!-- Lien vers la page contenant.php. -->
    <a href="contenu.php" class="btn-catalogue">
        Voir le catalogue complet
    </a>

</div>


<?php
// Charge le pied de page commun du site.
include 'includes/footer.php';
?>