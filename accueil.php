<?php 
require_once 'config/database.php';     /* Charge la configuration de la base de données require_once garantit que le fichier n'est inclus qu'une seule fois. */ 
include 'includes/header.php';  /* Insère le fichier d'en-tête du site  */

// Contenu mis en avant : aléatoire à chaque chargement
$requeteUne = $pdo->query("SELECT * FROM Contenu ORDER BY RAND() LIMIT 1");  /* Exécute une requète SQL qui sélectionne : SELECT * récupère toutes les colonne, ORDER BY RAND() mélange les résultats, LIMIT 1 conserve un seul résultat  */ 

$contenuUne = $requeteUne->fetch(); /* Récupère le contenu sélectionné sous forme de tableau associatif  */ 

// Aperçu de films
$requeteFilms = $pdo->query("SELECT * FROM Contenu WHERE Type = 'film' ORDER BY Annee DESC LIMIT 3"); /* Récupère les trois films les plus récents : WHERE Type = 'film' : filtre uniquelent les films, ORDER BY Annee DESC : trie par année décroissante   */ 
$films = $requeteFilms->fetchAll(); /* Récupère tous les films trouvés dans un tableau.  */

// Aperçu de séries

$requeteSeries = $pdo->query("SELECT * FROM Contenu WHERE Type = 'serie' ORDER BY Annee DESC LIMIT 3"); /* Répueère les trois séries les plus récentes : WHERE Type = 'serie' : filtre uniquement les séries, ORDER BY Annee DESC : trie par année décroisante       */ 
$series = $requeteSeries->fetchAll();  /* Récupère toutes les séries trouvées   */ 
?>


<!--------------------------------------------------------
    
        SECTION DU CONTENU MIS EN AVANT 

-->-------------------------------------------------------

<section class="a-la-une">

    <img src="Images/<?= htmlspecialchars($contenuUne['Affiche']) ?>" 

         alt="<?= htmlspecialchars($contenuUne['Titre']) ?>">   <!-- <_?_= : echo : version courte, $contenuUne['Affiche'] : contient le nom de l'affiche, htmlspecialchars() : protège le HTML contre les injection de code  -->


    <div class="infos-une">

        <h2><?= htmlspecialchars($contenuUne['Titre']) ?></h2> <!-- Affiche le titre du contenu -->

        <p><?= htmlspecialchars($contenuUne['Annee']) ?></p> <!-- Affiche son année de sortie -->

    </div>

</section>


<section class="decouverte">

    <h2>Films à découvrir</h2>


    <div class="liste-cartes">

        <?php foreach ($films as $film): ?> <!-- Parourt chaque film récupéré dans $films à chaque tour le film courant est stocké dans $film -->

            <div class="carte">

                <div class="carte-image">

                    <img src="Images/<?= htmlspecialchars($film['Affiche']) ?>" 

                         class="fond-flou" alt="">


                    <img src="Images/<?= htmlspecialchars($film['Affiche']) ?>" 

                         class="affiche-nette" 

                         alt="<?= htmlspecialchars($film['Titre']) ?>">

                </div>

                <h3><?= htmlspecialchars($film['Titre']) ?></h3>

                <p><?= htmlspecialchars($film['Annee']) ?></p>

            </div>

        <?php endforeach; ?>

    </div>

</section>


<section class="decouverte">

    <h2>Séries à découvrir</h2>


    <div class="liste-cartes">

        <?php foreach ($series as $serie): ?>

            <div class="carte">

                <div class="carte-image">
                    <img src="Images/<?= htmlspecialchars($serie['Affiche']) ?>" 

                         class="fond-flou" alt="">

                    <img src="Images/<?= htmlspecialchars($serie['Affiche']) ?>" 

                         class="affiche-nette" 
                         alt="<?= htmlspecialchars($serie['Titre']) ?>">

                </div>

                <h3><?= htmlspecialchars($serie['Titre']) ?></h3>

                <p><?= htmlspecialchars($serie['Annee']) ?></p>
            </div>

        <?php endforeach; ?>

    </div>

</section>


<!-- Un seul bouton pour toute la page -->

<div class="voir-plus">

    <a href="contenu.php" class="btn-catalogue">

        Voir le catalogue complet

    </a>

</div>


<?php include 'includes/footer.php'; ?>