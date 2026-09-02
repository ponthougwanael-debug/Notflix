<?php
session_start();    /* Démarre la session PHP afin d'accèder aux informations de connexion   */ 
require_once 'config/database.php'; /* Charge la connexion à la base de données    */



/*-------------------------------------------------------------------------------------------

      VERIFICATION DE LA CONNEXION 

---------------------------------------------------------------------------------------------*/










if (!isset($_SESSION['IdUtilisateur'])) {   /* Vérifie si l'identifiant de l'utilisateur est présent dans la session   */
    header('Location: includes/connexion.php');     /* Si l'utilisateur n'est pas connecté, il est redigiré vers la page de connexion, puis le script s'arrête   */
    exit;
}




 
$idUtilisateur = (int) $_SESSION['IdUtilisateur'];  /* Récupère l'identifiant de l'utilisateur et le convertit en entier   */



/*-------------------------------------------------------------------------------------------

        RECUPERATION DE LA LISTE 

---------------------------------------------------------------------------------------------*/


$stmt = $pdo->prepare(" 
    SELECT c.*, ml.DateAjout
    FROM ma_liste ml
    INNER JOIN Contenu c
        ON c.IdContenu = ml.IdContenu
    WHERE ml.IdUtilisateur = :utilisateur
    ORDER BY ml.DateAjout DESC
");     /* Récupère les informations des contenus; Récupère la date d'ajout à la liste; Relie ma_litse à contenu grâce à IdContenu; Sélectionne uniquement les contenus de l'utilisateur connecté; Affiche les contenus du plus récent au plus ancien   */

$stmt->execute([
    ':utilisateur' => $idUtilisateur
]);     /* Exécute la requète en associant l'identifiant de l'utilisateur au paramètre SQL   */

$contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);  /* Récupère tous les contenus sous forme de tableau associatifs   */

include 'includes/header.php';  /* Inclut l'en-tête commun de site   */
?>


<!--------------------------------------------------------------------------------------------

        AFFICHAGE DE LA LISTE 

-->


<h1>Ma liste</h1>   <!-- Affiche le titre de la page  -->

<div class="grille-contenus">   <!-- Crée un conteneur destiné à afficher les contenus sous forme de grille  -->

    <?php foreach ($contenus as $contenu): ?>   <!-- Parcourt chaque film ou serie enregistré dans la liste de l'utilisateur   -->

        <div class="carte-contenu">     <!-- Crée une carte pour son contenu     -->
            <img
                src="Images/<?= htmlspecialchars($contenu['Affiche']) ?>" 
                alt="<?= htmlspecialchars($contenu['Titre']) ?>"
            >   <!-- Affiche l'affiche du contenu     -->

            <h2><?= htmlspecialchars($contenu['Titre']) ?></h2> <!-- Affiche le titre du film ou de la série      -->

            <a href="details.php?id=<?= (int) $contenu['IdContenu'] ?>&type=<?= strtolower($contenu['Type']) ?>">   <!-- Crée un lien vers la page détail. L'url contient l'identifiant du contenu; son type, convertit en minuscule. Le cast int()  garantit que l'identifiant est traité comme un nombre    -->

                Voir les détails

            </a>
<!---------------------------------------------------------------------------------------------
            
            FORMULAIRE DE SUPPRESSION

----------------------------------------------------------------------------------------------->
            <form method="POST" action="supprimer-liste.php">   <!-- Crée un formulaire envoyé en méthode POST vers supprimer-liste.php     -->
                <input
                    type="hidden"
                    name="id_contenu"
                    value="<?= (int) $contenu['IdContenu'] ?>"
                >   <!-- Champs invisible contenant l'identifiant du contenu à supprimer   -->

                <button type="submit">Retirer</button>  <!-- Bouton permettant de retirer le contenu de la liste     -->
            </form>     <!-- Fermeture du formulaire     -->
        </div>  <!-- Ferme la carte contenu     -->
    <?php endforeach; ?>    <!-- Termine la boucle d'affichage    -->
</div>  <!-- Ferme la grille  -->

<?php include 'includes/footer.php'; ?> <!-- Inclut le pied de page commun du site     -->