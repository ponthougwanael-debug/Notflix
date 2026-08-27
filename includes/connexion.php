<?php
// Démarre la session pour pouvoir mémoriser l'utilisateur connecté.
session_start();

// Charge la connexion à la base de données.
require_once __DIR__ . '/../config/database.php';

// Variable qui contiendra les éventuels messages d'erreur.
$erreur = '';

// Vérifie si le formulaire a été envoyé.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et nettoie l'adresse e-mail saisie.
    $email = trim($_POST['email'] ?? '');

    // Récupère le mot de passe saisi.
    $motdepasse = $_POST['motdepasse'] ?? '';

    // Vérifie que tous les champs sont remplis.
    if ($email === '' || $motdepasse === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        // Prépare une requête sécurisée pour rechercher l'utilisateur.
        $requete = $pdo->prepare(
            'SELECT * FROM UTILISATEUR WHERE Email = ?'
        );

        // Exécute la requête avec l'adresse e-mail fournie.
        $requete->execute([$email]);

        // Récupère les informations de l'utilisateur.
        $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

        // Vérifie que l'utilisateur existe et que le mot de passe est correct.
        if (
            $utilisateur !== false
            && password_verify(
                $motdepasse,
                $utilisateur['MotDePasse']
            )
        ) {
            // Régénère l'identifiant de session pour améliorer la sécurité.
            session_regenerate_id(true);

            // Enregistre l'identifiant de l'utilisateur dans la session.
            $_SESSION['IdUtilisateur'] = $utilisateur['IdUtilisateur'];

            // Redirige l'utilisateur vers la page d'accueil.
            header('Location: ../accueil.php');
            exit;
        }

        // Message affiché si les identifiants sont incorrects.
        $erreur = 'Adresse e-mail ou mot de passe incorrect.';
    }
}

// Charge l'en-tête du site.
require_once __DIR__ . '/../includes/header.php';

// Réaffiche l'adresse e-mail saisie en empêchant les failles XSS.
$emailSaisi = htmlspecialchars(
    $_POST['email'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);
?>

<!-- Contenu principal de la page de connexion. -->
<main class="conteneur-connexion">
    <!-- Carte contenant le formulaire de connexion. -->
    <section class="carte-connexion">
        <h1>Connexion</h1>

        <!-- Affiche le message d'erreur s'il existe. -->
        <?php if ($erreur !== ''): ?>
            <p class="message-erreur">
                <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <!-- Formulaire de connexion. -->
        <form method="post" action="connexion.php">
            <!-- Champ de saisie de l'adresse e-mail. -->
            <label for="email">Adresse e-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= $emailSaisi ?>"
                required
            >

            <!-- Champ de saisie du mot de passe. -->
            <label for="motdepasse">Mot de passe</label>
            <input
                type="password"
                id="motdepasse"
                name="motdepasse"
                required
            >

            <!-- Bouton d'envoi du formulaire. -->
            <button type="submit">Se connecter</button>
        </form>

        <!-- Lien vers la page d'inscription. -->
        <p class="lien-inscription">
            Pas encore de compte ?
            <a href="inscription.php">Créer un compte</a>
        </p>
    </section>
</main>

<?php
// Charge le pied de page du site.
require_once __DIR__ . '/../includes/footer.php';
?>