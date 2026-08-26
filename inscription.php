<?php
// ==========================================
// CONNEXION À LA BASE DE DONNÉES
// ==========================================
require_once 'config/database.php';

// Tableau qui contiendra les messages d'erreur à afficher
$erreurs = [];

// Valeurs initiales des champs (pour réafficher le formulaire pré-rempli en cas d'erreur)
$nom = '';
$prenom = '';
$email = '';

// ==========================================
// TRAITEMENT DU FORMULAIRE (si soumis en POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage des champs (trim supprime les espaces inutiles)
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';

    // --- Vérification 1 : tous les champs sont-ils remplis ? ---
    if (
        empty($nom) ||
        empty($prenom) ||
        empty($email) ||
        empty($motdepasse)
    ) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }

    // --- Vérification 2 : l'email est-il valide ? ---
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide.";
    }

    // --- Vérification 3 : le mot de passe fait-il au moins 8 caractères ? ---
    if (!empty($motdepasse) && strlen($motdepasse) < 8) {
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    // --- Vérification 4 : l'email est-il déjà utilisé ? ---
    // On ne fait cette requête que si aucune erreur n'a été trouvée avant
    if (empty($erreurs)) {
        $stmt = $pdo->prepare(
            "SELECT * FROM UTILISATEUR WHERE Email = ?"
        );
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erreurs[] = "Cet email est déjà utilisé.";
        }
    }

    // --- Si toujours aucune erreur, on procède à l'inscription ---
    if (empty($erreurs)) {
        // Hachage sécurisé du mot de passe (jamais stocké en clair)
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);

        try {
            // Requête préparée pour insérer le nouvel utilisateur
            $stmt = $pdo->prepare(
                "INSERT INTO UTILISATEUR
                (Nom, Prenom, Email, MotDePasse)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $nom,
                $prenom,
                $email,
                $hash
            ]);

            // Inscription réussie : redirection vers la page de connexion
            header("Location: includes/connexion.php");
            exit;

        } catch (PDOException $e) {
            // Cas où l'insertion échoue malgré la vérification précédente
            // (ex : contrainte UNIQUE en base, insertion concurrente)
            // On journalise l'erreur réelle sans l'afficher à l'utilisateur
            error_log('Erreur inscription : ' . $e->getMessage());
            $erreurs[] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
        }
    }
}

// ==========================================
// AJOUT DE FEUILLES DE STYLE SPÉCIFIQUES À CETTE PAGE
// ==========================================
// On utilise la mise en tampon de sortie (output buffering) pour
// injecter des balises <link> juste avant la fermeture de </head>
// dans le HTML généré par includes/header.php
ob_start(static function (string $html): string {
    $liensCSS =
        "    <link rel=\"stylesheet\" href=\"css/style.css\">\n" .
        "    <link rel=\"stylesheet\" href=\"css/inscription.css\">\n";

    // Cherche la position de la balise </head> (insensible à la casse)
    $positionHead = stripos($html, '</head>');

    // Si la balise n'est pas trouvée, on retourne le HTML sans modification
    if ($positionHead === false) {
        return $html;
    }

    // Insère les liens CSS juste avant </head>
    return substr_replace($html, $liensCSS, $positionHead, 0);
});

// Inclusion de l'en-tête HTML (le callback ci-dessus s'applique à sa sortie)
include 'includes/header.php';
?>

<div class="conteneur-inscription">
    <div class="carte-inscription">
        <h2>Inscription</h2>

        <form method="POST">
            <!-- Champ Nom : valeur préservée en cas d'erreur -->
            <input
                type="text"
                name="nom"
                placeholder="Nom"
                value="<?= htmlspecialchars($nom) ?>"
                required
            >

            <!-- Champ Prénom -->
            <input
                type="text"
                name="prenom"
                placeholder="Prénom"
                value="<?= htmlspecialchars($prenom) ?>"
                required
            >

            <!-- Champ Email -->
            <input
                type="email"
                name="email"
                placeholder="Email"
                value="<?= htmlspecialchars($email) ?>"
                required
            >

            <!-- Champ Mot de passe : jamais réaffiché par sécurité -->
            <input
                type="password"
                name="motdepasse"
                placeholder="Mot de passe"
                required
            ><button type="submit">S'inscrire</button>
        </form>

        <!-- Affichage des éventuels messages d'erreur -->
        <?php foreach ($erreurs as $erreur): ?>
            <p class="message-erreur">
                <?= htmlspecialchars($erreur) ?>
            </p>
        <?php endforeach; ?>

        <p class="lien-connexion">
            Déjà un compte ?
            <a href="includes/connexion.php">Se connecter</a>
        </p>
    </div>
</div>

<?php
// Inclusion du pied de page
include 'includes/footer.php';

// Envoie le contenu du tampon (avec les CSS injectées) au navigateur
ob_end_flush();
?>