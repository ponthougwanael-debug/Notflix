<?php
session_start();

require_once 'config/database.php';

$typeUrl = strtolower(trim($_GET['type'] ?? ''));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || !in_array($typeUrl, ['film', 'série'], true)) {
    header('Location: accueil.php');
    exit;
}

// Valeur correspondant à la base de données
$typeBDD = $typeUrl === 'film' ? 'Film' : 'Série';

$stmt = $pdo->prepare("
    SELECT *
    FROM Contenu
    WHERE IdContenu = :id
    AND LOWER(Type) = LOWER(:type)
");

$stmt->execute([
    ':id' => $id,
    ':type' => $typeBDD
]);

$contenu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contenu) {
    header('Location: accueil.php');
    exit;
}

include 'includes/header.php';

$titre = htmlspecialchars($contenu['Titre']);
$affiche = htmlspecialchars($contenu['Affiche']);
$description = htmlspecialchars(
    $contenu['Description'] ?? 'Aucun synopsis disponible.'
);
$annee = htmlspecialchars($contenu['Annee'] ?? '-');
$labelType = $typeUrl === 'film' ? 'Film' : 'Série';
?>

<div class="conteneur-detail">
    <a href="javascript:history.back()" class="btn-retour">← Retour</a>

    <div class="fiche-detail">
        <img
            src="Images/<?= $affiche ?>"
            alt="<?= $titre ?>"
            class="affiche-detail"
            draggable="false"
            oncontextmenu="return false;"
        >

        <div class="infos-detail">
            <h1><?= $titre ?></h1>

            <span class="badge-type">
                <?= $labelType ?>
            </span>

            <p class="synopsis"><?= $description ?></p>

            <?php if ($typeUrl === 'film'): ?>
                <p>
                    <strong>Durée :</strong>
                    <?= htmlspecialchars($contenu['Duree'] ?? '-') ?> min
                </p>
            <?php endif; ?>

            <p>
                <strong>Année :</strong>
                <?= $annee ?>
            </p>
            <?php if (isset($_SESSION['IdUtilisateur'])): ?>

                <form method="POST" action="ajouter-liste.php">
                 <input
                     type="hidden"
                     name="id_contenu"
                    value="<?= (int) $contenu['IdContenu'] ?>"
                >

                     <button type="submit" class="btn-ajouter-liste">
                        + Ma liste
                    </button>
                </form>

                
                <?php else : ?>
                <a href="includes/connexion.php" class="btn-ajouter-liste">
                    Connectez-vous pour ajouter à ma liste
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>