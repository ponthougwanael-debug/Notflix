<?php
// Charge la connexion à la base de données.
require_once 'config/database.php';


// --------------------------------------------------
// RÉCUPÉRATION DES FILTRES ENVOYÉS PAR L'UTILISATEUR
// --------------------------------------------------

// Récupère le texte recherché dans l'URL.
// Si aucun texte n'est fourni, une chaîne vide est utilisée.
// trim() supprime les espaces inutiles au début et à la fin.
$recherche = trim($_GET['q'] ?? '');

// Récupère le type sélectionné : Film ou Série.
$type = trim($_GET['type'] ?? '');

// Récupère l'identifiant du genre sélectionné.
$idGenre = trim($_GET['genre'] ?? '');


// --------------------------------------------------
// RÉCUPÉRATION DES GENRES
// --------------------------------------------------

// Prépare une requête pour récupérer tous les genres.
$requeteGenres = $pdo->query("
    SELECT IdGenre, NomGenre
    FROM genre
    ORDER BY NomGenre ASC
");

// Récupère les genres sous forme de tableau associatif.
$genres = $requeteGenres->fetchAll(PDO::FETCH_ASSOC);


// Recherche le nom du genre correspondant à l'identifiant sélectionné.
// Cette information sera affichée dans le résumé de la recherche.
$nomGenreSelectionne = '';

foreach ($genres as $genre) {

    // Compare les identifiants sous forme de texte.
    // Cela évite un problème si l'un est un nombre et l'autre une chaîne.
    if ((string) $genre['IdGenre'] === (string) $idGenre) {

        // Sauvegarde le nom du genre trouvé.
        $nomGenreSelectionne = $genre['NomGenre'];

        // Arrête la boucle dès que le genre est trouvé.
        break;
    }
}


// --------------------------------------------------
// CONSTRUCTION DE LA REQUÊTE PRINCIPALE
// --------------------------------------------------

// Sélectionne tous les contenus et le nom de leur genre.
// LEFT JOIN permet de garder les contenus même s'ils n'ont pas de genre.
$sql = "
    SELECT contenu.*, genre.NomGenre
    FROM contenu
    LEFT JOIN genre ON contenu.IdGenre = genre.IdGenre
    WHERE 1 = 1
";

// Tableau qui contiendra les valeurs des paramètres SQL.
$params = [];


// Ajoute une condition si l'utilisateur a saisi une recherche.
if ($recherche !== '') {

    // Recherche le texte dans le titre du contenu.
    // LIKE permet de rechercher une partie du titre.
    $sql .= " AND contenu.Titre LIKE :recherche";

    // Les caractères % permettent de trouver le texte
    // même s'il se trouve au début ou au milieu du titre.
    $params[':recherche'] = '%' . $recherche . '%';
}


// Ajoute une condition si un type a été sélectionné.
if ($type !== '') {

    // Filtre les contenus selon leur type.
    $sql .= " AND contenu.Type = :type";

    // Associe la valeur du formulaire au paramètre SQL.
    $params[':type'] = $type;
}


// Ajoute une condition si un genre a été sélectionné.
if ($idGenre !== '') {

    // Filtre les contenus selon l'identifiant du genre.
    $sql .= " AND contenu.IdGenre = :genre";

    // Associe l'identifiant du genre au paramètre SQL.
    $params[':genre'] = $idGenre;
}


// Trie les résultats par titre, dans l'ordre alphabétique.
$sql .= " ORDER BY contenu.Titre ASC";


// Prépare la requête SQL construite précédemment.
$requete = $pdo->prepare($sql);

// Exécute la requête avec les paramètres sélectionnés.
$requete->execute($params);

// Récupère tous les contenus trouvés.
$toutContenu = $requete->fetchAll(PDO::FETCH_ASSOC);


// Charge l'en-tête du site.
include 'includes/header.php';
?>


<!--
    Formulaire de recherche.
    La méthode GET place les filtres dans l'URL.
    Cela permet de conserver et de partager la recherche.
-->
<form method="GET" action="contenu.php" class="form-recherche" id="formRecherche">

    <!-- Champ permettant de rechercher un titre. -->
    <input
        type="search"
        id="rechercheLocale"
        name="q"
        placeholder="Rechercher un film ou une série..."
        value="<?= htmlspecialchars($recherche) ?>"
    >

    <!-- Liste permettant de filtrer par type. -->
    <select name="type" id="filtreType">

        <!-- Option par défaut : tous les types. -->
        <option value="">Films et séries</option>

        <!-- Option pour afficher uniquement les films. -->
        <option value="Film" <?= $type === 'Film' ? 'selected' : '' ?>>
            Films
        </option>

        <!-- Option pour afficher uniquement les séries. -->
        <option value="Série" <?= $type === 'Série' ? 'selected' : '' ?>>
            Séries
        </option>

    </select>


    <!-- Liste permettant de filtrer par genre. -->
    <select name="genre" id="filtreGenre">

        <!-- Option par défaut : tous les genres. -->
        <option value="">Tous les genres</option>

        <!-- Parcourt tous les genres récupérés dans la base de données. -->
        <?php foreach ($genres as $genre): ?>

            <option
                value="<?= (int) $genre['IdGenre'] ?>"
                <?= (string) $idGenre === (string) $genre['IdGenre'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($genre['NomGenre']) ?>
            </option>

        <?php endforeach; ?>

    </select>


    <!-- Envoie le formulaire avec les filtres sélectionnés. -->
    <button type="submit">
        Rechercher
    </button>

</form>


<!-- Conteneur des résultats. -->
<div id="resultatsContenu">


<?php
// Vérifie si au moins un filtre a été utilisé.
if ($recherche !== '' || $type !== '' || $idGenre !== ''):
?>

    <!-- Affiche un résumé des filtres utilisés. -->
    <p class="resultat-recherche">
        Résultats correspondant à :

        <?php if ($recherche !== ''): ?>
            <!-- Affiche le texte recherché. -->
            <strong><?= htmlspecialchars($recherche) ?></strong>
        <?php endif; ?>


        <?php if ($type !== ''): ?>
            <!-- Affiche le type sélectionné. -->
            - Type :
            <strong><?= htmlspecialchars($type) ?></strong>
        <?php endif; ?>


        <?php if ($nomGenreSelectionne !== ''): ?>
            <!-- Affiche le nom du genre sélectionné. -->
            — Genre :
            <strong><?= htmlspecialchars($nomGenreSelectionne) ?></strong>
        <?php endif; ?>

    </p>

<?php endif; ?>


<main>
    <section class="decouverte">

        <!--
            Modifie le titre selon qu'il s'agit d'une recherche
            ou de l'affichage complet du catalogue.
        -->
        <h2>
            <?php if ($recherche !== '' || $type !== '' || $idGenre !== ''): ?>
                Résultats de la recherche
            <?php else: ?>
                Tous nos films et séries
            <?php endif; ?>
        </h2>


        <?php if (empty($toutContenu)): ?>

            <!-- Message affiché si aucun résultat n'a été trouvé. -->
            <p class="aucun-resultat">
                Aucun film ou série trouvé.
            </p>

        <?php else: ?>

            <!-- Conteneur qui regroupe toutes les cartes. -->
            <div class="liste-cartes">

                <!--
                    Parcourt chaque contenu trouvé.
                    Le contenu actuel est stocké dans la variable $item.
                -->
                <?php foreach ($toutContenu as $item): ?>

                    <!-- Carte représentant un film ou une série. -->
                    <div class="carte">

                        <div class="carte-image">

                            <!-- Image floutée utilisée comme arrière-plan. -->
                            <img
                                src="Images/<?= htmlspecialchars($item['Affiche']) ?>"
                                class="fond-flou"
                                alt=""
                            >

                            <!-- Image nette affichée au premier plan. -->
                            <img
                                src="Images/<?= htmlspecialchars($item['Affiche']) ?>"
                                class="affiche-nette"
                                alt="<?= htmlspecialchars($item['Titre']) ?>"
                            >

                        </div>


                        <!-- Affiche le titre du contenu. -->
                        <h3>
                            <?= htmlspecialchars($item['Titre']) ?>
                        </h3>


                        <!-- Affiche l'année de sortie. -->
                        <p>
                            <?= htmlspecialchars($item['Annee']) ?>
                        </p>


                        <!-- Affiche le type uniquement s'il existe. -->
                        <?php if (!empty($item['Type'])): ?>
                            <p>
                                Type :
                                <?= htmlspecialchars($item['Type']) ?>
                            </p>
                        <?php endif; ?>


                        <!-- Affiche le genre uniquement s'il existe. -->
                        <?php if (!empty($item['NomGenre'])): ?>
                            <p>
                                Genre :
                                <?= htmlspecialchars($item['NomGenre']) ?>
                            </p>
                        <?php endif; ?>


                        <!--
                            Lien vers la page de détails.
                            urlencode() encode le type pour l'utiliser dans l'URL.
                            (int) garantit que l'identifiant est un nombre entier.
                        -->
                        <a
                            href="details.php?type=<?= urlencode(strtolower($item['Type'])) ?>&id=<?= (int) $item['IdContenu'] ?>"
                            class="btn-voir-detail"
                        >
                            Voir le détail
                        </a>

                    </div>

                <!-- Fin de la boucle des contenus. -->
                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>
</main>

</div>


<script>
// Attend que toute la page HTML soit chargée.
document.addEventListener('DOMContentLoaded', function () {

    // Récupère le formulaire de recherche.
    const formulaire = document.getElementById('formRecherche');

    // Arrête le script si le formulaire n'existe pas.
    if (!formulaire) {
        return;
    }

    // Détecte l'envoi du formulaire.
    formulaire.addEventListener('submit', function () {

        // Récupère le bouton d'envoi du formulaire.
        const bouton = formulaire.querySelector(
            'button[type="submit"]'
        );

        // Vérifie que le bouton existe.
        if (bouton) {

            // Désactive le bouton pour éviter plusieurs envois.
            bouton.disabled = true;

            // Modifie le texte du bouton pendant la recherche.
            bouton.textContent = 'Recherche...';
        }
    });
});
</script>


<?php
// Charge le pied de page du site.
include 'includes/footer.php';
?>