<?php

require_once 'config/database.php';

$recherche = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$idGenre = trim($_GET['genre'] ?? '');

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

$resultats = $requete->fetchAll(PDO::FETCH_ASSOC);

if (empty($resultats)) {
    echo '<p class="aucun-resultat">Aucun film ou série trouvé.</p>';
    exit;
}

foreach ($resultats as $item):
?>

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