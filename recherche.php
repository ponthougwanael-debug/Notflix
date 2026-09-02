<?php

require_once 'config/database.php';     /* Charge la connexion à la base de donnée stockée dans $pdo    */ 



/*--------------------------------------------------------------------------------------------

        RECUPERATION DES FILTRES

----------------------------------------------------------------------------------------------*/
$recherche = trim($_GET['q'] ?? ''); /* Récupère le texte recherché dans l'url avec q. Trim() supprime les espaces inutiles. Si aucune recherche n'est fourni, la valeur est vide*/
$type = trim($_GET['type'] ?? '');      /* Récupère le type : film ou série   */
$idGenre = trim($_GET['genre'] ?? '');  /* Récupère l'identifiant du genre   */

$sql = "
    SELECT contenu.*, genre.NomGenre
    FROM contenu
    LEFT JOIN genre ON contenu.IdGenre = genre.IdGenre
    WHERE 1 = 1
";  /* Récupère toutes les colonne de la table contenu; Récupère le nom du genre; Relie les tables contenu et le genre; Utilise LEFT JOIN pour conserver les contenus même sans genre associé WHERE 1 = 1 permet d'ajouter facilement des conditions avec AND   */ 

$params = [];   /* Crée un tableau qui contiendra les valeurs des paramètres SQL   */

/* --------------------------------------------------------------------------------------------

        FILTRE PAR TITRE 

----------------------------------------------------------------------------------------------*/


if ($recherche !== '') {    /* Vérifie si l'utilisateur a saisi une recherche     */
    $sql .= " AND contenu.Titre LIKE :recherche";   /* Ajoute une condition pour rechercher dans le titre   */
    $params[':recherche'] = '%' . $recherche . '%'; /* Le % permettent de trouver le texte même s'il se situe au millieu du titre */
    }
    
/*--------------------------------------------------------------------------------------------

        FILTRE PAR TYPE

----------------------------------------------------------------------------------------------*/

if ($type !== '') {     /* Vérifie si le type a été sélectionné   */
    $sql .= " AND contenu.Type = :type";    /* Ajoute un filtre correspondant au type choisi et stocke la valeur   */
    $params[':type'] = $type;   /*    */
}

/*--------------------------------------------------------------------------------------------

        FILTRE PAR GENRE

----------------------------------------------------------------------------------------------*/


if ($idGenre !== '') {  /* Vérifie si un genre a été sélectionné   */
    $sql .= " AND contenu.IdGenre = :genre";    /* Ajoute un filtre sur l'identifiant du genre */
    $params[':genre'] = $idGenre;
}

/*--------------------------------------------------------------------------------------------

        TRI DES RESULTATS

----------------------------------------------------------------------------------------------*/


$sql .= " ORDER BY contenu.Titre ASC";  /* Trie les résultats par titre, dans l'ordre alphabétique   */

$requete = $pdo->prepare($sql);     /* Prépare la requète SQL   */
$requete->execute($params);     /* Exécute la requète en remplçant les paramètres par leurs valeurs   */


/*--------------------------------------------------------------------------------------------

        CETTE METHODE AIDE Ä PROTËGER LE CODE CONTRE LES INJECTIONS SQL

----------------------------------------------------------------------------------------------*/

$resultats = $requete->fetchAll(PDO::FETCH_ASSOC);  /* Récupère le résultats sous forme de tableau associatif    */



/*-------------------------------------------------------------------------------------------- 

        GESTION D'UNE RECHERCHE VIDE 

----------------------------------------------------------------------------------------------*/



if (empty($resultats)) {    /* Vérifie si aucun résultat n'a été trouvé   */
    echo '<p class="aucun-resultat">Aucun film ou série trouvé.</p>';   /* Affiche un message puis arrête le script   */
    exit;
}

/*-------------------------------------------------------------------------------------------- 

       AFFICHAGE DES RESULTATS

----------------------------------------------------------------------------------------------*/


foreach ($resultats as $item):  /* Parcourt chaque film ou série trouvé    */
?>

<div class="carte">     <!-- Crée une carte HTML pour chaque contenu     -->

    <div class="carte-image">  
        <img
            src="Images/<?= htmlspecialchars($item['Affiche']) ?>"
            class="fond-flou"
            alt=""
        >  <!-- Affiche l'affiche en arrière-plan flou     -->

        <img
            src="Images/<?= htmlspecialchars($item['Affiche']) ?>"
            class="affiche-nette"
            alt="<?= htmlspecialchars($item['Titre']) ?>"
        >   <!-- Affiche l’affiche nette.     -->
    </div>

    <h3>
        <?= htmlspecialchars($item['Titre']) ?>     <!-- Affiche le titre du contenu     -->
    </h3>

    <p>
        <?= htmlspecialchars($item['Annee']) ?>     <!-- Affiche l'année de sortie     -->
    </p>

<!--------------------------------------------------------------------------------------------

        AFFICHAGE CONDITIONNEL DU TYPE

----------------------------------------------------------------------------------------------->



    <?php if (!empty($item['Type'])): ?>    <!-- Vérffie si le type existe     -->
        <p>
            Type :
            <?= htmlspecialchars($item['Type']) ?>  <!-- Affiche le type : film ou serie     -->
        </p>
    <?php endif; ?> <!-- Termine la condition     -->


<!--------------------------------------------------------------------------------------------

        AFFICHAGE CONDITIONNEL DU GENRE

----------------------------------------------------------------------------------------------->


    <?php if (!empty($item['NomGenre'])): ?>    <!-- Vérifie si un genre est disponible     -->
        <p>
            Genre :
            <?= htmlspecialchars($item['NomGenre']) ?>  <!-- Affiche le nom du genre     -->
        </p>
    <?php endif; ?> <!-- Termine la condition     -->

<!-------------------------------------------------------------------------------------------

            LIEN VERS LA PAGE DE DETAIL

----------------------------------------------------------------------------------------------->



    <a
        href="details.php?type=<?= urlencode(strtolower($item['Type'])) ?>&id=<?= (int) $item['IdContenu'] ?>" 
        class="btn-voir-detail"
    >   <!-- urlencode() encode correctement le type dans l'url, tandis que int() convertit l'identifiant en entier     -->
        Voir le détail
    </a> 

</div>

<?php endforeach; ?>    <!-- Termine la boucle qui affiche tous les résultats     -->