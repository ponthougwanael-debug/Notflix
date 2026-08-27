<?php
session_start();
require_once 'config/database.php';


if (!isset($_SESSION['IdUtilisateur'])) {
    header('Location: includes/connexion.php');
    exit;
}

$idUtilisateur = (int) $_SESSION['IdUtilisateur'];

$stmt = $pdo->prepare("
    SELECT c.*, ml.DateAjout
    FROM ma_liste ml
    INNER JOIN Contenu c
        ON c.IdContenu = ml.IdContenu
    WHERE ml.IdUtilisateur = :utilisateur
    ORDER BY ml.DateAjout DESC
");

$stmt->execute([
    ':utilisateur' => $idUtilisateur
]);

$contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<h1>Ma liste</h1>

<!-- ID utilise par le JavaScript pour cibler la grille de ma liste. -->
<div id="grilleMaListe" class="grille-contenus">
    <?php foreach ($contenus as $contenu): ?>
        <div class="carte-contenu">
            <img
                src="Images/<?= htmlspecialchars($contenu['Affiche']) ?>"
                alt="<?= htmlspecialchars($contenu['Titre']) ?>"
            >

            <h2><?= htmlspecialchars($contenu['Titre']) ?></h2>

            <a href="details.php?id=<?= (int) $contenu['IdContenu'] ?>&type=<?= strtolower($contenu['Type']) ?>">
                Voir les détails
            </a>

            <form method="POST" action="supprimer-liste.php">
                <input
                    type="hidden"
                    name="id_contenu"
                    value="<?= (int) $contenu['IdContenu'] ?>"
                >

                <button type="submit">Retirer</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<!-- Inclusion du script de gestion de la grille, avant le footer (qui ferme la page). -->
<script src="js/ma-liste.js" defer></script>

<?php include 'includes/footer.php'; ?>