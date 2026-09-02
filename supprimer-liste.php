<?php
session_start();    /* Démarre la session afin d'accéder aux informations de l'utilisateur connecté   */

require_once 'config/database.php';     /* Charge la connexion à la base de données, stockée dans $pdo   */



/*-------------------------------------------------------------------------------------------- 

        VERIFICATION DE LA CONNEXION

----------------------------------------------------------------------------------------------*/


if (!isset($_SESSION['IdUtilisateur'])) {
    header('Location: includes/connexion.php');
    exit;
}   /* Vérifie si l'identifiant est absent de la session. Si l'utilisateur n'est pas connecté : il est redigéré vers la page de connexion. Exit permet d'arreter le script immédiatement   */


/*--------------------------------------------------------------------------------------------

        RECUPERATION DES IDENTIFIANTS

----------------------------------------------------------------------------------------------*/



$idUtilisateur = (int) $_SESSION['IdUtilisateur'];  /* Récupère l'identifiant est le convertit en entier   */
$idContenu = filter_input(INPUT_POST, 'id_contenu', FILTER_VALIDATE_INT);   /* Récupère l'identifiant du contenu envoyé par le formulaire avec la méthode POST. La valeur est validée pour vérifier qu'il s'agit bien d'un entier   */

if (!$idContenu) {  /* Vérifie si l'identifiznt est invalide ou absent   */
    header('Location: ma-liste.php');
    exit;
}   /* Dans ce cas, l'utilisateur est renvoyé vers sa liste   */ 


/*--------------------------------------------------------------------------------------------

        SUPRESSION DANS LA BASE DE DONNEES

----------------------------------------------------------------------------------------------*/



$stmt = $pdo->prepare("
    DELETE FROM ma_liste
    WHERE IdUtilisateur = :utilisateur
    AND IdContenu = :contenu
");     /* ¨Prépare une requète SQL qui supprime dans la table ma_liste. La suppression concerne uniquement la ligne correspondant : à l'utilisateur connecté ; au contenu sélectionné. Les paramètres nommés protègent la requete contre les injections SQL   */

$stmt->execute([
    ':utilisateur' => $idUtilisateur,
    ':contenu' => $idContenu
]);     /* Exécute la requète en remplaçant les paramètres par les identifiants correspondant   */


/*--------------------------------------------------------------------------------------------

        REDIRECTION FINALE 
----------------------------------------------------------------------------------------------*/

header('Location: ma-liste.php'); /* Après suppression, l'utilisateur est redirig" vers ma-liste.php */
exit;