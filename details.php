<?php
session_start(); /* Démmarre ou reprend une session PHP. Cela permet de savoir si un utilisateur est connectée */ 

require_once 'config/database.php'; /* Charge la connecion à la base de données stockée dans $pdo  */ 

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

$titre = htmlspecialchars($contenu['Titre']);   /* Protège et prépare le titre pour l'affichage HTML  */
$affiche = htmlspecialchars($contenu['Affiche']);   /* Protège le nom du fichier image  */
$description = htmlspecialchars(
    $contenu['Description'] ?? 'Aucun synopsis disponible.'
);  /* Récupère la description. Si elle est absente, affiche le message 'Aucun synopsis disponible' */
$annee = htmlspecialchars($contenu['Annee'] ?? '-');    /* Récupère l'année. Si elle est absente affiche '-' */
$labelType = $typeUrl === 'film' ? 'Film' : 'Série';    /* Prépare le type à afficher à l'écran */
?>

<!----------------------------------------------------------------------------- 
 
        STRUCTURE HTML PRINCIPALE 

------------------------------------------------------------------------------- -->

<div class="conteneur-detail">  <!-- Crée le conteneur principale de la page de détails    -->
    <a href="javascript:history.back()" class="btn-retour">← Retour</a> <!-- Crée un bouton permettant de revenir à la page précédente    --> 

    <div class="fiche-detail">  <!-- Contient l'affiche et les information du contenu  -->
        <img
            src="Images/<?= $affiche ?>" 
            alt="<?= $titre ?>"
            class="affiche-detail"
            draggable="false"
            oncontextmenu="return false;"
        >   <!-- Affiche l'image située dans le dossier Images. Utilise le titre comme texte alternatif de l'image. Applique le style CSS de l'affiche détaillée Empêche l'image d'être déplacée par glisser-déposer. Désactive le menu contextuel au clic droit sur l'image "     -->

        <div class="infos-detail">  <!-- Contient les informations textuelles     -->
            <h1><?= $titre ?></h1>  <!-- Affiche le titre principale du contenu     -->

            <span class="badge-type">   <!-- Affiche un badge indiquant s'il s'agit d'un film ou d'une série     -->
                <?= $labelType ?>
            </span>

            <p class="synopsis"><?= $description ?></p>     <!-- Affiche le synposis     -->
<!------------------------------------------------------------------------------------ 
            AFFICHAGE DE LA DUREE
------------------------------------------------------------------------------------->

            <?php if ($typeUrl === 'film'): ?>  <!-- Commence une condition : la durée sera affichée uniquement pour les films     -->
                <p>
                    <strong>Durée :</strong>    <!-- Affiche le mot "durée" en gras -->
                    <?= htmlspecialchars($contenu['Duree'] ?? '-') ?> min   <!-- Affiche la durée du film en minutes. Si elle est absente, affiche "-"     -->
                </p>
            <?php endif; ?>     <!-- Termine la condition.     -->

<!-- --------------------------------------------------------------------------------

        AFFICHAGE DE L'ANNEE

------------------------------------------------------------------------------------->

            <p>
                <strong>Année :</strong>    <!-- Affiche le libellé en gras     -->
                <?= $annee ?>   <!-- Affiche l'année préparée précedemment      -->

            </p>
<!-- --------------------------------------------------------------------------------

      GESTION DE LA LISTE PERSONNELLE

------------------------------------------------------------------------------------->



            <?php if (isset($_SESSION['IdUtilisateur'])): ?>    <!-- Vérifie si l'utilisateur est connecté. La session contient IdUtilisateur lorsq'une connexion a été établie-->

                <form method="POST" action="ajouter-liste.php">     <!-- Crée un formulaire qui envoie les données avec la méthode POST vers ajouter-liste.php     -->
                 <input
                     type="hidden"
                     name="id_contenu"
                    value="<?= (int) $contenu['IdContenu'] ?>"
                >   <!-- Envoie discrètement l'identifiant du contenu. Type hidden rend le champ invisible. int() convertit la valeur en entier     -->

                     <button type="submit" class="btn-ajouter-liste">   <!-- Crée le bouton permettant d'ajouter le contenu à la liste personelle     -->
                        + Ma liste
                    </button>
                </form>

                
                <?php else : ?> <!-- S'éxécute si l'utilisateur n'est pas connecté     -->
                <a href="includes/connexion.php" class="btn-ajouter-liste"> <!-- Affiche un lien vers la page de connexion     -->
                    Connectez-vous pour ajouter à ma liste
                </a>
            <?php endif; ?>     <!-- Termine la condition de connexion     -->
        </div>
    </div>      <!-- Ferme les conteneurs d’informations et de fiche  -->
</div>  <!-- Ferme le conteneur principal     -->

<?php include 'includes/footer.php'; ?>     <!-- Insère le pied de page commun du site     -->