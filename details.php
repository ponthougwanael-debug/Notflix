<?php
// ==========================================
// DÉMARRAGE DE LA SESSION
// ==========================================
// Nécessaire pour vérifier si l'utilisateur est connecté (bouton "Ma liste")
session_start();

// Connexion à la base de données ($pdo)
require_once 'config/database.php';

// ==========================================
// RÉCUPÉRATION ET VALIDATION DES PARAMÈTRES GET
// ==========================================
// strtolower() + trim() : uniformise la casse et supprime les espaces parasites
$typeUrl = strtolower(trim($_GET['type'] ?? ''));

// filter_input valide que "id" est bien un entier (retourne false ou null sinon)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Vérifie que l'ID est valide ET que le type est bien "film" ou "serie"
// (sans accent, pour rester cohérent avec accueil.php et contenu.php)
if (!$id || !in_array($typeUrl, ['film', 'serie'], true)) {
    header('Location: accueil.php');
    exit;
}

// ==========================================
// CORRESPONDANCE AVEC LA VALEUR EN BASE DE DONNÉES
// ==========================================
// Ici on garde la même valeur que celle utilisée dans accueil.php / contenu.php
$typeBDD = $typeUrl; // 'film' ou 'serie'

// ==========================================
// RÉCUPÉRATION DU CONTENU DEMANDÉ
// ==========================================
// Requête préparée : sécurise contre les injections SQL
// LOWER() des deux côtés : évite les soucis de casse (Film/film/FILM)
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

// Récupère la ligne correspondante sous forme de tableau associatif
$contenu = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucun contenu ne correspond (mauvais ID ou type), on redirige
if (!$contenu) {
    header('Location: accueil.php');
    exit;
}

// ==========================================
// INCLUSION DE L'EN-TÊTE HTML
// ==========================================
// Placé après les redirections pour éviter l'erreur "headers already sent"
include 'includes/header.php';

// ==========================================
// PRÉPARATION DES VARIABLES D'AFFICHAGE
// ==========================================
// htmlspecialchars() protège contre les failles XSS
$titre = htmlspecialchars($contenu['Titre']);
$affiche = htmlspecialchars($contenu['Affiche']);

// Valeur par défaut si la description est vide en base
$description = htmlspecialchars(
    $contenu['Description'] ?? 'Aucun synopsis disponible.'
);

// Valeur par défaut si l'année est vide en base
$annee = htmlspecialchars($contenu['Annee'] ?? '-');

// Label affiché à l'écran (avec majuscule, pour l'esthétique)
$labelType = $typeUrl === 'film' ? 'Film' : 'Série';
?>

<div class="conteneur-detail">
    <!-- Bouton retour utilisant l'historique de navigation du navigateur -->
    <a href="javascript:history.back()" class="btn-retour">← Retour</a>

    <div class="fiche-detail">
        <!-- Affiche du film/série -->
        <img
            src="Images/<?= $affiche ?>"
            alt="<?= $titre ?>"
            class="affiche-detail"
            draggable="false"
            oncontextmenu="return false;"
        >

        <div class="infos-detail">
            <h1><?= $titre ?></h1>

            <!-- Badge indiquant si c'est un Film ou une Série -->
            <span class="badge-type">
                <?= $labelType ?>
            </span>

            <p class="synopsis"><?= $description ?></p>

            <!-- Affiche la durée uniquement si c'est un film -->
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

            <!-- ==========================================
                 BOUTON "MA LISTE"
                 Affiché uniquement si l'utilisateur est connecté
                 ========================================== -->
            <?php if (isset($_SESSION['IdUtilisateur'])): ?>

                <form method="POST" action="ajouter-liste.php">
                    <!-- Champ caché contenant l'ID du contenu à ajouter -->
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
                <!-- Si non connecté : invitation à se connecter -->
                <a href="includes/connexion.php" class="btn-ajouter-liste">
                    Connectez-vous pour ajouter à ma liste
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
// Inclusion du pied de page HTML
include 'includes/footer.php'; 
?>