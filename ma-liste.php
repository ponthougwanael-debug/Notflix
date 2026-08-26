<?php
// ==========================================
// DÉMARRAGE DE LA SESSION
// ==========================================
session_start();

// Connexion à la base de données ($pdo)
require_once 'config/database.php';

// ==========================================
// VÉRIFICATION DE LA CONNEXION UTILISATEUR
// ==========================================
// Cette page est réservée aux utilisateurs connectés
if (!isset($_SESSION['IdUtilisateur'])) {
    header('Location: includes/connexion.php');
    exit;
}

// Récupération de l'ID utilisateur en session, casté en entier par sécurité
$idUtilisateur = (int) $_SESSION['IdUtilisateur'];

// ==========================================
// RÉCUPÉRATION DES CONTENUS DE "MA LISTE"
// ==========================================
try {
    // Jointure entre ma_liste et Contenu pour récupérer les infos complètes
    // des contenus ajoutés par l'utilisateur, triés du plus récent au plus ancien
    $stmt = $pdo->prepare("
        SELECT c.*, ml.DateAjout
        FROM ma_liste ml
        INNER JOIN Contenu c
            ON c.IdContenu = ml.IdContenu
        WHERE ml.IdUtilisateur = :utilisateur
        ORDER BY ml.DateAjout DESC
    ");

    $stmt->execute([
        ':utilisateur' => $idUtilisateur
    ]);

    // Récupère tous les résultats sous forme de tableau associatif
    $contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En cas d'erreur SQL, on journalise et on affiche une liste vide
    // plutôt que de faire planter la page ou d'exposer l'erreur brute
    error_log('Erreur récupération ma_liste : ' . $e->getMessage());
    $contenus = [];
}

// Inclusion de l'en-tête HTML (menu, styles, etc.)
include 'includes/header.php';
?>

<h1>Ma liste</h1>

<div class="grille-contenus">
    <?php if (empty($contenus)): ?>
        <!-- Message affiché si l'utilisateur n'a encore rien ajouté -->
        <p class="message-vide">
            Votre liste est vide pour le moment. Ajoutez des films ou séries depuis leur page de détails !
        </p>

    <?php else: ?>
        <?php foreach ($contenus as $contenu): ?>
            <div class="carte-contenu">
                <!-- Affiche du contenu -->
                <img
                    src="Images/<?= htmlspecialchars($contenu['Affiche']) ?>"
                    alt="<?= htmlspecialchars($contenu['Titre']) ?>"
                >

                <!-- Titre du contenu -->
                <h2><?= htmlspecialchars($contenu['Titre']) ?></h2>

                <!-- Lien vers la page de détails, avec type et id échappés -->
                <a href="details.php?id=<?= (int) $contenu['IdContenu'] ?>&type=<?= htmlspecialchars(strtolower($contenu['Type'])) ?>">
                    Voir les détails
                </a>

                <!-- Formulaire pour retirer le contenu de la liste -->
                <form method="POST" action="supprimer-liste.php">
                    <input
                        type="hidden"
                        name="id_contenu"
                        value="<?= (int) $contenu['IdContenu'] ?>"
                    >

                    <button type="submit">Retirer</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>