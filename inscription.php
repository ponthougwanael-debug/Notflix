<?php
require_once 'config/database.php';

$erreurs = [];

$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';

    if (
        empty($nom) ||
        empty($prenom) ||
        empty($email) ||
        empty($motdepasse)
    ) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide.";
    }

    if (!empty($motdepasse) && strlen($motdepasse) < 8) {
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare(
            "SELECT * FROM UTILISATEUR WHERE Email = ?"
        );
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erreurs[] = "Cet email est déjà utilisé.";
        }
    }

    if (empty($erreurs)) {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);

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

        header("Location: includes/connexion.php");
        exit;
    }
}

// Inclusion de l'en-tête après le traitement PHP
ob_start(static function (string $html): string {
    $liensCSS =
        "    <link rel=\"stylesheet\" href=\"css/style.css\">\n" .
        "    <link rel=\"stylesheet\" href=\"css/inscription.css\">\n";

    $positionHead = stripos($html, '</head>');

    if ($positionHead === false) {
        return $html;
    }

    return substr_replace($html, $liensCSS, $positionHead, 0);
});

include 'includes/header.php';
?>

<div class="conteneur-inscription">
    <div class="carte-inscription">
        <h2>Inscription</h2>

        <form method="POST">
            <input
                type="text"
                name="nom"
                placeholder="Nom"
                value="<?= htmlspecialchars($nom) ?>"
                required
            >

            <input
                type="text"
                name="prenom"
                placeholder="Prénom"
                value="<?= htmlspecialchars($prenom) ?>"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Email"
                value="<?= htmlspecialchars($email) ?>"
                required
            >

            <input
                type="password"
                name="motdepasse"
                placeholder="Mot de passe"
                required
            >

            <button type="submit">S'inscrire</button>
        </form>

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
include 'includes/footer.php';
ob_end_flush();
?>