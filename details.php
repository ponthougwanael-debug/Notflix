<?php
session_start(); /* Démmarre ou reprend une session PHP. Cela permet de savoir si un utilisateur est connectée */ 

require_once 'config/database.php'; /* Charge la connecion à la base de données  */ 

$typeUrl = strtolower(trim($_GET['type'] ?? ''));   /* Récupère le paramètre type dans l'url, ?? '' utilise une chaine vide si le paramètre n'existe pas, trim() : supprime les espaces inutiles, strtolower() : convertit le texte en minuscule  */ 
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);   /* Récupère et vérifie que les paramètre id est bien un nombre entier  */

if (!$id || !in_array($typeUrl, ['film', 'série'], true)) {  /* Vérifie si un identifiant existe, le type est soit un $film ou une $serie */ 
    header('Location: accueil.php'); /* Si les paramètre sont invalide, l'utilisateur est redigiré vers accueil.php, puis le script s'arrête  */ 
    exit;
}

// Valeur correspondant à la base de données


$typeBDD = $typeUrl === 'film' ? 'Film' : 'Série'; /* Convertit le type d'url en valeur utilisé dan la base de donnée    */ 

$stmt = $pdo->prepare(" 
    SELECT * 
    FROM Contenu
    WHERE IdContenu = :id
    AND LOWER(Type) = LOWER(:type)
");     /* Prépare une requète SQL pour récuperer un contenu prècis, SELECT * : recupère toute les colonnes, IdContenu = :id : recherche l'identifiant demandé, :id et type : sont des paramètes protégés, LOWER() : permet de comparer les types sans tenir compte des majuscules. L'utilisation de prepare() protège contre les injections SQL.   */

$stmt->execute([
    ':id' => $id,
    ':type' => $typeBDD
]);     /* Exécute la requète en associant les valeurs aux paramètres SQL  */

$contenu = $stmt->fetch(PDO::FETCH_ASSOC); /* Récupère le résultat sosu forme de tableau associatif  */ 

if (!$contenu) {
    header('Location: accueil.php');
    exit;
}   /* Si le contenu n'est pas trouvé, redirection vers accueil.php   */

include 'includes/header.php';  /* Insère l'en-tête du site */


/* ---------------------------------------------------------------------------- 

        PREPARATION DES DONNEES

 ------------------------------------------------------------------------------ */


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