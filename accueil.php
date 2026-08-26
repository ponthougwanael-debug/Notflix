<?php 
require_once 'config/database.php';
include 'includes/header.php';

// Contenu mis en avant : aléatoire à chaque chargement
$requeteUne = $pdo->query("SELECT * FROM Contenu ORDER BY RAND() LIMIT 1");
$contenuUne = $requeteUne->fetch();

// Aperçu de films
$requeteFilms = $pdo->query("SELECT * FROM Contenu WHERE Type = 'film' ORDER BY Annee DESC LIMIT 3");
$films = $requeteFilms->fetchAll();

// Aperçu de séries
$requeteSeries = $pdo->query("SELECT * FROM Contenu WHERE Type = 'serie' ORDER BY Annee DESC LIMIT 3");
$series = $requeteSeries->fetchAll();
?>

<section class="a-la-une">
    <img src="Images/<?= htmlspecialchars($contenuUne['Affiche']) ?>" 
         alt="<?= htmlspecialchars($contenuUne['Titre']) ?>">

    <div class="infos-une">
        <h2><?= htmlspecialchars($contenuUne['Titre']) ?></h2>
        <p><?= htmlspecialchars($contenuUne['Annee']) ?></p>
    </div>
</section>

<section class="decouverte">
    <h2>Films à découvrir</h2>

    <div class="liste-cartes">
        <?php foreach ($films as $film): ?>
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