<?php
// ==========================================
// CONFIGURATION DU MODE DEBUG
// ==========================================
// Passez cette variable à false en production pour ne pas exposer
// les erreurs PHP détaillées aux visiteurs (faille de sécurité potentielle)
$modeDebug = true;

if ($modeDebug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Connexion à la base de données (fichier séparé pour la sécurité/réutilisabilité)
require_once 'config/database.php';

// ==========================================
// RÉCUPÉRATION DES PARAMÈTRES DE RECHERCHE (GET)
// ==========================================
// trim() supprime les espaces avant/après
// ?? '' évite une erreur si le paramètre n'existe pas dans l'URL
$recherche = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$idGenre = trim($_GET['genre'] ?? '');

// ==========================================
// RÉCUPÉRATION DE LA LISTE DES GENRES (pour le <select>)
// ==========================================
// Pas de paramètre utilisateur ici, donc query() simple suffit (pas d'injection possible)
$requeteGenres = $pdo->query("
    SELECT IdGenre, NomGenre
    FROM genre
    ORDER BY NomGenre ASC
");
$genres = $requeteGenres->fetchAll(PDO::FETCH_ASSOC);

// On cherche le nom du genre sélectionné (pour l'affichage du résumé de recherche)
$nomGenreSelectionne = '';
foreach ($genres as $genre) {
    if ((string) $genre['IdGenre'] === (string) $idGenre) {
        $nomGenreSelectionne = $genre['NomGenre'];
        break;
    }
}

// ==========================================
// CONSTRUCTION DYNAMIQUE DE LA REQUÊTE SQL
// ==========================================
// On part d'une base "WHERE 1 = 1" pour pouvoir ajouter des "AND" facilement
$sql = "
    SELECT contenu.*, genre.NomGenre
    FROM contenu
    LEFT JOIN genre ON contenu.IdGenre = genre.IdGenre
    WHERE 1 = 1
";

$params = [];

// Si l'utilisateur a tapé un mot-clé, on filtre sur le titre (recherche partielle avec LIKE)
if ($recherche !== '') {
    $sql .= " AND contenu.Titre LIKE :recherche";
    $params[':recherche'] = '%' . $recherche . '%';
}

// Si un type (film/série) est choisi, on filtre dessus
// ATTENTION : vérifiez que 'film'/'serie' correspond bien à la casse utilisée dans votre base
if ($type !== '') {
    $sql .= " AND contenu.Type = :type";
    $params[':type'] = $type;
}

// Si un genre est choisi, on filtre dessus
if ($idGenre !== '') {
    $sql .= " AND contenu.IdGenre = :genre";
    $params[':genre'] = $idGenre;
}

$sql .= " ORDER BY contenu.Titre ASC";

// Requête préparée = protection contre les injections SQL
$requete = $pdo->prepare($sql);
$requete->execute($params);
$toutContenu = $requete->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<form method="GET" action="contenu.php" class="form-recherche" id="formRecherche">

    <!-- Champ de recherche texte -->
    <input
        type="search"
        id="rechercheLocale"
        name="q"
        placeholder="Rechercher un film ou une série..."
        value="<?= htmlspecialchars($recherche) ?>"
    >

    <!-- Filtre par type : valeurs en minuscules pour correspondre à la base -->
    <select name="type" id="filtreType">
        <option value="">Films et séries</option>

        <option value="film" <?= $type === 'film' ? 'selected' : '' ?>>
            Films
        </option>

        <option value="serie" <?= $type === 'serie' ? 'selected' : '' ?>>
            Séries
        </option>
    </select>

    <!-- Filtre par genre, généré dynamiquement depuis la base -->
    <select name="genre" id="filtreGenre">
        <option value="">Tous les genres</option>

        <?php foreach ($genres as $genre): ?>
            <option
                value="<?= (int) $genre['IdGenre'] ?>"
                <?= (string) $idGenre === (string) $genre['IdGenre'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($genre['NomGenre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">
        Rechercher
    </button>

</form>

<div id="resultatsContenu">

<?php if ($recherche !== '' || $type !== '' || $idGenre !== ''): ?>
    <!-- Résumé des critères de recherche actifs -->
    <p class="resultat-recherche">
        Résultat correspondant à :

        <?php if ($recherche !== ''): ?>
            <strong><?= htmlspecialchars($recherche) ?></strong>
        <?php endif; ?>

        <?php if ($type !== ''): ?>
            - Type : <strong><?= htmlspecialchars($type) ?></strong>
        <?php endif; ?>

        <?php if ($nomGenreSelectionne !== ''): ?>
            — Genre : <strong><?= htmlspecialchars($nomGenreSelectionne) ?></strong>
        <?php endif; ?>
    </p>
<?php endif; ?>

    <main>
        <section class="decouverte">
            <h2>
                <?php if ($recherche !== '' || $type !== '' || $idGenre !== ''): ?>
                    Résultat de la recherche
                <?php else: ?>
                    Tous nos films et Séries
                <?php endif; ?>
            </h2>

            <?php if (empty($toutContenu)): ?>

                <!-- Aucun résultat trouvé -->
                <p class="aucun-resultat">
                    Aucun film ou série trouvé.
                </p>

            <?php else: ?>

                <div class="liste-cartes">

                    <?php foreach ($toutContenu as $item): ?>

                        <?php
                        // Image par défaut si le champ Affiche est vide en base
                        $affiche = !empty($item['Affiche']) ? $item['Affiche'] : 'default.jpg';
                        ?>

                        <div class="carte">

                            <div class="carte-image">
                                <img
                                    src="Images/<?= htmlspecialchars($affiche) ?>"
                                    class="fond-flou"
                                    alt=""
                                >
                                <img
                                    src="Images/<?= htmlspecialchars($affiche) ?>"
                                    class="affiche-nette"
                                    alt="<?= htmlspecialchars($item['Titre']) ?>"
                                >
                            </div>

                            <h3>
                                <?= htmlspecialchars($item['Titre']) ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($item['Annee']) ?>
                            </p>

                            <?php if (!empty($item['Type'])): ?>
                                <p>
                                    Type :
                                    <?= htmlspecialchars($item['Type']) ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($item['NomGenre'])): ?>
                                <p>
                                    Genre :
                                    <?= htmlspecialchars($item['NomGenre']) ?>
                                </p>
                            <?php endif; ?>

                            <a
                                href="details.php?type=<?= urlencode(strtolower($item['Type'])) ?>&id=<?= (int) $item['IdContenu'] ?>"
                                class="btn-voir-detail"
                            >
                                Voir le détail
                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>
        </section>
    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formulaire = document.getElementById('formRecherche');

    if (!formulaire) {
        return;
    }

    // Désactive le bouton pendant l'envoi pour éviter les doubles clics/soumissions
    formulaire.addEventListener('submit', function () {
        const bouton = formulaire.querySelector('button[type="submit"]');

        if (bouton) {
            bouton.disabled = true;
            bouton.textContent = 'Recherche...';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>