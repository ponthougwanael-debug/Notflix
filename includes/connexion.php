<?php

session_start();

require_once __DIR__ . '/../config/database.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';

    if ($email === '' || $motdepasse === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT * FROM UTILISATEUR WHERE Email = ?'
        );

        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $utilisateur &&
            password_verify($motdepasse, $utilisateur['MotDePasse'])
        ) {
            $_SESSION['IdUtilisateur'] = $utilisateur['IdUtilisateur'];
            $_SESSION['Prenom'] = $utilisateur['Prenom'];

            header('Location: ../contenu.php');
            exit;
        }

        $erreur = 'Email ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion - Notflix</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/connexion.css">
</head>

<body>

<div class="conteneur-connexion">
    <div class="carte-connexion">

        <h2>Connexion</h2>

        <form method="POST" action="connexion.php">

            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >

            <input
                type="password"
                name="motdepasse"
                placeholder="Mot de passe"
                required
            >

            <button type="submit">
                Se connecter
            </button>

        </form>

        <?php if ($erreur !== ''): ?>
            <p class="message-erreur">
                <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <p class="lien-inscription">
            Pas encore de compte ?
            <a href="../inscription.php">S'inscrire</a>
        </p>

    </div>
</div>

</body>
</html>