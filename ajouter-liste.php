<?php
session_start();    /* Démarre la session PHP afin d'accéder aux information de connexion   */

require_once 'config/database.php';     /* Charge la connexion à la base de données grâce a la $pdo   */


/*--------------------------------------------------------------------------------------------

        VERIFICATION DE LA CONNEXION

----------------------------------------------------------------------------------------------*/


if (!isset($_SESSION['IdUtilisateur'])) {   /* Vérifie si l'utilisateur est connecté    */
    header('Location: includes/connexion.php');     /* S'il ne l'est pas , il est redirigé vers la page de connexion, puis le script s'arrète   */
    exit;
}

$idUtilisateur = (int) $_SESSION['IdUtilisateur'];  /* Récupère l'identifiant de l'utilisateur et le convertit en entier   */
$idContenu = filter_input(INPUT_POST, 'id_contenu', FILTER_VALIDATE_INT);   /* Récupère l'identifiant du contenu envouyé par le formulaire et vérifie qu'il s'agit bien d'un nombre entier   */


/*--------------------------------------------------------------------------------------------

        VERIFICATION DE L'IDENTIFIANT

----------------------------------------------------------------------------------------------*/


if (!$idContenu) {  /* Si l'identifiant est absent ou invalide, l'utilisateur est renvoyé vers la page d'accueil  */
    header('Location: accueil.php');
    exit;
}

/*----------------------------------------------------------------------------------------------

        GESTION DES ERREURS

----------------------------------------------------------------------------------------------*/


try {
    // Évite d'ajouter deux fois le même contenu
    $verification = $pdo->prepare("
        SELECT COUNT(*)
        FROM ma_liste
        WHERE IdUtilisateur = :utilisateur
        AND IdContenu = :contenu
    ");     /* Prépare une requète SQL qui compte le nombre de fois oû le contenu est dèja présent dans la liste de cet utilisateur  */

    $verification->execute([
        ':utilisateur' => $idUtilisateur,
        ':contenu' => $idContenu
    ]);     /* Exécute la requête avec l'identifiant de l'utilisateur et celui du contenu    */

    if ($verification->fetchColumn() == 0) {    /* Récupètre le résultat du COUNT(*) et si le résultat vaut zéro, le contenu n'est pas encore dans la liste */
        $ajout = $pdo->prepare(" 
            INSERT INTO ma_liste (IdUtilisateur, IdContenu)
            VALUES (:utilisateur, :contenu)
        ");     /* Prépare une requête pour insérer le contenu dans la table ma_liste */

        $ajout->execute([
            ':utilisateur' => $idUtilisateur,
            ':contenu' => $idContenu
        ]);
    }   /* Exécute l'insertion avec les identifiants correspondants    */

    header('Location: ma-liste.php');   /* Après l'ajout, l'utilisateur est redirigé vers sa liste personelle Même si le contenu existe dèja il est également redirigé vers cette page  */
    exit;

} catch (PDOException $e) {     /* Capture les erreurs provenant de PDO ou de la base de données  */
    die('Erreur lors de l’ajout : ' . $e->getMessage());
}   /* Arrète le programmee et affiche le message d'erreur     */