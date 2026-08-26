<?php
/**
 * Page de recherche renvoyant uniquement les cartes de contenu (AJAX ou appel direct).
 */

// ==========================================
// FONCTION D'ÉCHAPPEMENT HTML CENTRALISÉE
// ==========================================
function e($valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ==========================================
// RÉCUPÉRATION ET VALIDATION DES PARAMÈTRES GET
// ==========================================

// Terme de recherche (protège contre un tableau envoyé en ?q[]=...)
$rechercheParam = $_GET['q'] ?? '';
$recherche = is_string($rechercheParam) ? trim($rechercheParam) : '';

// Type de contenu, normalisé en minuscule sans accent
$typeParam = $_GET['type'] ?? '';
$type = is_string($typeParam) ? strtolower(trim($typeParam)) : '';

// Seules ces deux valeurs sont acceptées, sinon on ignore le filtre
if (!in_array($type, ['film', 'serie'], true)) {
    $type = '';
}

// Identifiant de genre, validé comme entier positif
$genreParam = $_GET['genre'] ?? '';
$idGenre = null;

if (is_string($genreParam) && $genreParam !== '') {
    $genreValide = filter_var($genreParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($genreValide !== false) {
        $idGenre = (int) $genreValide;
    }
}

// ==========================================
// CONNEXION À LA BASE DE DONNÉES
// ==========================================
require_once 'config/database.php';

// ==========================================
// CONSTRUCTION DE LA REQUÊTE SQL (avec paramètres liés, pas de concaténation)
// ==========================================
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

if ($idGenre !== null) {
    $sql .= " AND contenu.IdGenre = :genre";
    $params[':genre'] = $idGenre;
}

$sql .= " ORDER BY contenu.Titre ASC";

// ==========================================
// EXÉCUTION SÉCURISÉE AVEC GESTION D'ERREUR
// ==========================================
try {
    $requete = $pdo->prepare($sql);

    foreach ($params as $nom => $valeur) {
        $requete->bindValue($nom, $valeur, $nom === ':genre' ? PDO::PARAM_INT : PDO::PARAM_STR);
    }

    $requete->execute();
    $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    // On journalise l'erreur, jamais affichée à l'utilisateur
    error_log('Erreur PDO dans recherche.php : ' . $exception->getMessage());
    http_response_code(500);
    echo '<p class="aucun-resultat">Une erreur est survenue lors de la recherche.</p>';
    exit;
}

// Aucun résultat trouvé
if (empty($resultats)) {
    echo '<p class="aucun-resultat">Aucun film ou série trouvé.</p>';
    exit;
}

// ==========================================
// AFFICHAGE DES RÉSULTATS (toutes les valeurs échappées)
// ==========================================
foreach ($resultats as $item):
    $affiche = e($item['Affiche'] ?? '');
    $titre = e($item['Titre'] ?? '');
    $annee = e($item['Annee'] ?? '');
    $typeItem = strtolower(trim((string) ($item['Type'] ?? '')));
    $nomGenre = e($item['NomGenre'] ?? '');

    // Libellé affiché à l'utilisateur (peut garder l'accent), différent du type technique
    $libelleType = match ($typeItem) {
        'film' => 'Film',
        'serie' => 'Série',
        default => '',
    };

    $idContenu = (int) ($item['IdContenu'] ?? 0);

    // URL construite proprement puis échappée pour le HTML
    $urlDetail = 'details.php?' . http_build_query(
        ['type' => $typeItem, 'id' => $idContenu],
        '', '&', PHP_QUERY_RFC3986
    );
?>
    <div class="carte">
        <div class="carte-image">
            <img src="Images/<?= $affiche ?>" class="fond-flou" alt="">
            <img src="Images/<?= $affiche ?>" class="affiche-nette" alt="<?= $titre ?>">
        </div>

        <h3><?= $titre ?></h3>
        <p><?= $annee ?></p>

        <?php if ($libelleType !== ''): ?>
            <p>Type : <?= e($libelleType) ?></p>
        <?php endif; ?>

        <?php if ($nomGenre !== ''): ?>
            <p>Genre : <?= $nomGenre ?></p>
        <?php endif; ?>

        <a href="<?= e($urlDetail) ?>" class="btn-voir-detail">Voir le détail</a>
    </div>
<?php endforeach; ?>