<?php // Début du code PHP

require_once 'config/database.php'; // Charge la connexion à la base de données

$erreurs = []; // Crée un tableau pour stocker les erreurs

$nom = ''; // Initialise le nom
$prenom = ''; // Initialise le prénom
$email = ''; // Initialise l'adresse e-mail

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérifie si le formulaire a été envoyé

    $nom = trim($_POST['nom'] ?? ''); // Récupère et nettoie le nom
    $prenom = trim($_POST['prenom'] ?? ''); // Récupère et nettoie le prénom
    $email = trim($_POST['email'] ?? ''); // Récupère et nettoie l'e-mail
    $motdepasse = $_POST['motdepasse'] ?? ''; // Récupère le mot de passe

    if ( // Commence la vérification des champs
        empty($nom) || // Vérifie si le nom est vide
        empty($prenom) || // Vérifie si le prénom est vide
        empty($email) || // Vérifie si l'e-mail est vide
        empty($motdepasse) // Vérifie si le mot de passe est vide
    ) { // Fin de la condition
        $erreurs[] = "Tous les champs sont obligatoires."; // Ajoute un message d'erreur
    } // Fin de la vérification des champs

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) { // Vérifie le format de l'e-mail
        $erreurs[] = "Email invalide."; // Ajoute une erreur si l'e-mail est incorrect
    } // Fin de la vérification de l'e-mail

    if (!empty($motdepasse) && strlen($motdepasse) < 8) { // Vérifie que le mot de passe contient au moins 8 caractères
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères."; // Ajoute une erreur
    } // Fin de la vérification du mot de passe

    if (empty($erreurs)) { // Continue seulement s'il n'y a aucune erreur

        $stmt = $pdo->prepare( // Prépare une requête SQL
            "SELECT * FROM UTILISATEUR WHERE Email = ?" // Recherche un utilisateur avec cet e-mail
        ); // Termine la préparation de la requête

        $stmt->execute([$email]); // Exécute la requête avec l'e-mail fourni

        if ($stmt->fetch()) { // Vérifie si un utilisateur a été trouvé
            $erreurs[] = "Cet email est déjà utilisé."; // Ajoute une erreur si l'e-mail existe
        } // Fin de la vérification de l'e-mail existant
    } // Fin de la vérification des erreurs

    if (empty($erreurs)) { // Continue si aucune erreur n'existe

        $hash = password_hash($motdepasse, PASSWORD_DEFAULT); // Sécurise le mot de passe

        $stmt = $pdo->prepare( // Prépare la requête d'insertion
            "INSERT INTO UTILISATEUR // Indique la table à utiliser
            (Nom, Prenom, Email, MotDePasse) // Indique les colonnes
            VALUES (?, ?, ?, ?)" // Indique les valeurs à insérer
        ); // Termine la préparation

        $stmt->execute([ // Exécute l'insertion
            $nom, // Insère le nom
            $prenom, // Insère le prénom
            $email, // Insère l'e-mail
            $hash // Insère le mot de passe chiffré
        ]); // Termine l'insertion

        header("Location: includes/connexion.php"); // Redirige vers la connexion
        exit; // Arrête le script
    } // Fin de l'inscription
} // Fin du traitement POST

ob_start(static function (string $html): string { // Commence la mise en mémoire de la page

    $liensCSS = // Crée les liens vers les fichiers CSS
        "    <link rel=\"stylesheet\" href=\"css/style.css\">\n" . // Ajoute le CSS général
        "    <link rel=\"stylesheet\" href=\"css/inscription.css\">\n"; // Ajoute le CSS de l'inscription

    $positionHead = stripos($html, '</head>'); // Cherche la balise fermante head

    if ($positionHead === false) { // Vérifie si la balise head est absente
        return $html; // Retourne la page sans modification
    } // Fin de la vérification

    return substr_replace($html, $liensCSS, $positionHead, 0); // Ajoute les CSS avant </head>
}); // Termine la fonction de mise en mémoire

include 'includes/header.php'; // Affiche l'en-tête
?>

<div class="conteneur-inscription"> <!-- Crée le conteneur principal -->
    <div class="carte-inscription"> <!-- Crée la carte d'inscription -->

        <h2>Inscription</h2> <!-- Affiche le titre -->

        <form method="POST"> <!-- Crée le formulaire envoyé en POST -->

            <input type="text" name="nom" placeholder="Nom"
                   value="<?= htmlspecialchars($nom) ?>" required> <!-- Champ du nom -->

            <input type="text" name="prenom" placeholder="Prénom"
                   value="<?= htmlspecialchars($prenom) ?>" required> <!-- Champ du prénom -->

            <input type="email" name="email" placeholder="Email"
                   value="<?= htmlspecialchars($email) ?>" required> <!-- Champ de l'e-mail -->

            <input type="password" name="motdepasse"
                   placeholder="Mot de passe" required> <!-- Champ du mot de passe -->

            <button type="submit">S'inscrire</button> <!-- Bouton d'inscription -->

        </form> <!-- Ferme le formulaire -->

        <?php foreach ($erreurs as $erreur): ?> <!-- Parcourt les erreurs -->
            <p class="message-erreur"> <!-- Crée un paragraphe d'erreur -->
                <?= htmlspecialchars($erreur) ?> <!-- Affiche l'erreur en sécurité -->
            </p> <!-- Ferme le paragraphe -->
        <?php endforeach; ?> <!-- Termine la boucle -->

        <p class="lien-connexion"> <!-- Crée le texte de connexion -->
            Déjà un compte ? <!-- Affiche le message -->
            <a href="includes/connexion.php">Se connecter</a> <!-- Crée le lien de connexion -->
        </p> <!-- Ferme le paragraphe -->

    </div> <!-- Ferme la carte -->
</div> <!-- Ferme le conteneur -->

<?php
include 'includes/footer.php'; // Affiche le pied de page
ob_end_flush(); // Envoie le contenu mémorisé au navigateur
?> <!-- Fin du code PHP -->