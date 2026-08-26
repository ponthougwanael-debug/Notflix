<?php 
// ==========================================
// CONNEXION À LA BASE DE DONNÉES ET EN-TÊTE
// ==========================================
// Charge la connexion PDO ($pdo) définie dans ce fichier
require_once 'config/database.php';

// Inclut l'en-tête HTML commun à toutes les pages (balises <head>, menu de navigation, etc.)
include 'includes/header.php';

// ==========================================
// CONTENU "À LA UNE" (aléatoire)
// ==========================================
// ORDER BY RAND() : trie les lignes de la table dans un ordre aléatoire
// LIMIT 1 : ne garde qu'une seule ligne => un contenu différent à chaque rechargement de page
$requeteUne = $pdo->query("SELECT * FROM Contenu ORDER BY RAND() LIMIT 1");

// fetch() (sans "All") récupère uniquement la première ligne du résultat sous forme de tableau associatif
$contenuUne = $requeteUne->fetch();

// ==========================================
// APERÇU DE 3 FILMS RÉCENTS
// ==========================================
// WHERE Type = 'film' : ne garde que les lignes correspondant à des films
// ORDER BY Annee DESC : trie du plus récent au plus ancien
// LIMIT 3 : ne garde que les 3 premiers résultats
$requeteFilms = $pdo->query("SELECT * FROM Contenu WHERE Type = 'film' ORDER BY Annee DESC LIMIT 3");

// fetchAll() récupère toutes les lignes du résultat sous forme de tableau de tableaux associatifs
$films = $requeteFilms->fetchAll();

// ==========================================
// APERÇU DE 3 SÉRIES RÉCENTES
// ==========================================
// Même logique que pour les films, mais avec Type = 'serie'
$requeteSeries = $pdo->query("SELECT * FROM Contenu WHERE Type = 'serie' ORDER BY Annee DESC LIMIT 3");
$series = $requeteSeries->fetchAll();
?>

<!-- ==========================================
     SECTION "À LA UNE"
     Affiche en grand le contenu tiré au hasard
     ========================================== -->
<section class="a-la-une">
    <!-- htmlspecialchars() protège contre les failles XSS en échappant les caractères spéciaux HTML -->
    <img src="Images/<?= htmlspecialchars($contenuUne['Affiche']) ?>" 
         alt="<?= htmlspecialchars($contenuUne['Titre']) ?>">

    <div class="infos-une">
        <h2><?= htmlspecialchars($contenuUne['Titre']) ?></h2>
        <p><?= htmlspecialchars($contenuUne['Annee']) ?></p>
    </div>
</section>

<!-- ==========================================
     SECTION "FILMS À DÉCOUVRIR"
     Affiche les 3 films récupérés plus haut
     ========================================== -->
<section class="decouverte">
    <h2>Films à découvrir</h2>

    <div class="liste-cartes">
        <?php foreach ($films as $film): ?>
            <!-- Une "carte" par film -->
            <div class="carte">
                <div class="carte-image">
                    <!-- Image floutée en fond (effet décoratif en CSS) -->
                    <img src="Images/<?= htmlspecialchars($film['Affiche']) ?>" 
                         class="fond-flou" alt="">

                    <!-- Image nette affichée par-dessus -->
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

<!-- ==========================================
     SECTION "SÉRIES À DÉCOUVRIR"
     Même principe que la section films
     ========================================== -->
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

<!-- ==========================================
     BOUTON VERS LE CATALOGUE COMPLET
     Un seul bouton pour toute la page, en bas
     ========================================== -->
<div class="voir-plus">
    <a href="contenu.php" class="btn-catalogue">
        Voir le catalogue complet
    </a>
</div>

<?php 
// Inclut le pied de page HTML commun à toutes les pages
include 'includes/footer.php'; 
?>