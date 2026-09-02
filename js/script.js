// Récupère le champ de recherche grâce à son identifiant HTML.
const champRecherche = document.querySelector('#rechercheLocale');

// Récupère toutes les cartes qui possèdent la classe "carte".
const cartes = document.querySelectorAll('.carte');

// Récupère le message indiquant qu'aucun résultat n'a été trouvé.
const aucunResultat = document.querySelector('#aucunResultat');


// Vérifie que le champ de recherche existe bien dans la page.
if (champRecherche) {

    // Exécute cette fonction chaque fois que l'utilisateur modifie le champ.
    champRecherche.addEventListener('input', () => {

        // Récupère le texte saisi par l'utilisateur.
        // toLowerCase() convertit le texte en minuscules.
        // trim() supprime les espaces inutiles au début et à la fin.
        const recherche = champRecherche.value.toLowerCase().trim();

        // Compteur du nombre de cartes correspondant à la recherche.
        let nombreResultats = 0;


        // Parcourt toutes les cartes une par une.
        cartes.forEach(carte => {

            // Cherche le titre h3 présent dans la carte.
            const titre = carte.querySelector('h3')?.textContent

                // Convertit le titre en minuscules.
                .toLowerCase()

                // Supprime les espaces inutiles.
                .trim()

                // Si aucun titre n'est trouvé, utilise une chaîne vide.
                || '';


            // Vérifie si le titre contient le texte recherché.
            if (titre.includes(recherche)) {

                // Affiche la carte.
                // Une valeur vide permet de réutiliser son affichage CSS normal.
                carte.style.display = '';

                // Augmente le nombre de résultats trouvés.
                nombreResultats++;

            } else {

                // Cache la carte si son titre ne correspond pas.
                carte.style.display = 'none';
            }
        });


        // Vérifie que le message "aucun résultat" existe.
        if (aucunResultat) {

            // Affiche le message si aucun résultat n'est trouvé.
            // Sinon, il reste masqué.
            aucunResultat.style.display =
                nombreResultats === 0 ? 'block' : 'none';
        }
    });
}