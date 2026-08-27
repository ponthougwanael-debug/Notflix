// Récupère le champ de recherche local.
const champRecherche = document.querySelector('#rechercheLocale');

// Récupère toutes les cartes affichées sur la page.
const cartes = document.querySelectorAll('.carte');

// Récupère le message affiché lorsqu'aucun résultat n'est trouvé.
const aucunResultat = document.querySelector('#aucunResultat');

// Vérifie que le champ de recherche existe avant d'ajouter un événement.
if (champRecherche) {
    // Détecte chaque modification effectuée dans le champ de recherche.
    champRecherche.addEventListener('input', () => {
        // Récupère la recherche, la convertit en minuscules
        // et supprime les espaces inutiles.
        const recherche = champRecherche.value.toLowerCase().trim();

        // Compteur du nombre de cartes correspondantes.
        let nombreResultats = 0;

        // Parcourt toutes les cartes disponibles.
        cartes.forEach(carte => {
            // Récupère le titre de la carte.
            // Le symbole ?. évite une erreur si le titre n'existe pas.
            const titre = carte.querySelector('h3')?.textContent
                .toLowerCase()
                .trim() || '';

            // Vérifie si le titre contient le texte recherché.
            if (titre.includes(recherche)) {
                // Affiche la carte correspondante.
                carte.style.display = '';

                // Augmente le nombre de résultats trouvés.
                nombreResultats++;
            } else {
                // Masque la carte qui ne correspond pas à la recherche.
                carte.style.display = 'none';
            }
        });

        // Vérifie que le message "aucun résultat" existe.
        if (aucunResultat) {
            // Affiche le message s'il n'y a aucun résultat,
            // sinon le masque.
            aucunResultat.style.display =
                nombreResultats === 0 ? 'block' : 'none';
        }
    });
}