<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

$recherche = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$idGenre = trim($_GET['genre'] ?? '');

$requeteGenres = $pdo->query("
    SELECT IdGenre, NomGenre
    FROM genre
    ORDER BY NomGenre ASC
");

$genres = $requeteGenres->fetchAll(PDO::FETCH_ASSOC);

$nomGenreSelectionne = '';

foreach ($genres as $genre) {
    if ((string) $genre['IdGenre'] === (string) $idGenre) {
        $nomGenreSelectionne = $genre['NomGenre'];
        break;
    }
}

$sql = "
    SELECT contenu.*, genre.NomGenre
    FROM contenu
    LEFT JOIN genre ON contenu.IdGenre = genre.IdGenre
    WHERE 1 = 1
";

$params = [];

if ($recherche !== '') {
    $sql .= " AND contenu.Titre LIKE :recherche";
    $params[':recherche'] = '%' . $recherche . '%';
}

if ($type !== '') {
    $sql .= " AND contenu.Type = :type";
    $params[':type'] = $type;
}

if ($idGenre !== '') {
    $sql .= " AND contenu.IdGenre = :genre";
    $params[':genre'] = $idGenre;
}

$sql .= " ORDER BY contenu.Titre ASC";

$requete = $pdo->prepare($sql);
$requete->execute($params);

$toutContenu = $requete->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>


<form method="GET" action="contenu.php" class="form-recherche" id="formRecherche">

    <input
        type="search"
        id="rechercheLocale"
        name="q"
        placeholder="Rechercher un film ou une série..."
        value="<?= htmlspecialchars($recherche) ?>"
    >

    <select name="type" id="filtreType">
        <option value="">Films et séries</option>

        <option value="Film" <?= $type === 'Film' ? 'selected' : '' ?>>
            Films
        </option>

        <option value="Série" <?= $type === 'Série' ? 'selected' : '' ?>>
            Séries
        </option>
    </select>

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

<?php if ($recherche !== '' || $type !== '' || $idGenre !== '' ): ?>
    <p class="resultat-recherche">
        Résultat correspondant à :

        <?php if ($recherche !== ''): ?>
            <strong><?= htmlspecialchars($recherche) ?></strong>
        <?php endif; ?>

        <?php if ($type !== ''): ?>
            - Type : <strong><?= htmlspecialchars($type) ?></strong>
        <?php endif; ?>

        <?php if ($nomGenreSelectionne !== ''): ?>
            — Genre :
                     <strong><?= htmlspecialchars($nomGenreSelectionne) ?></strong>
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

                <p class="aucun-resultat">
                    Aucun film ou série trouvé.
                </p>

            <?php else: ?>

                <div class="liste-cartes">

                    <?php foreach ($toutContenu as $item): ?>

                        <div class="carte">

                            <div class="carte-image">
                                <img
                                    src="Images/<?= htmlspecialchars($item['Affiche']) ?>"
                                    class="fond-flou"
                                    alt=""
                                >

                                <img
                                    src="Images/<?= htmlspecialchars($item['Affiche']) ?>"
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

    formulaire.addEventListener('submit', function () {
        const bouton = formulaire.querySelectorh2('button[type="submit"]');

        if (bouton) {
            bouton.disabled = true;
            bouton.textContent = 'Recherche...';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>