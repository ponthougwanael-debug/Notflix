/*

 *   Retire un contenu de « Ma liste » sans recharger la page.
 *   Le script utilise la structure actuelle de ma-liste.php :
 *  .grille-contenus pour la grille ;
 *   .carte-contenu pour chaque carte ;
 *   un formulaire POST vers supprimer-liste.php ;
 *   un bouton « Retirer » dans chaque formulaire.
 */

(function () {
    'use strict';

    // Attend que le HTML soit entièrement disponible avant de chercher la grille.
    function initialiserSuppression() {
        // #grilleMaListe est le sélecteur prévu si un id est ajouté à la grille.
        // .grille-contenus est le sélecteur de repli présent dans ma-liste.php fourni.
        const grille = document.querySelector('#grilleMaListe, .grille-contenus');

        // La page peut ne pas contenir de grille : dans ce cas, il n'y a rien à faire.
        if (!grille) {
            return;
        }

        // La délégation d'événement fonctionne aussi si les cartes sont générées par PHP.
        grille.addEventListener('submit', function (evenement) {
            const formulaire = evenement.target.closest('form');

            if (!formulaire || !grille.contains(formulaire)) {
                return;
            }

            // Vérifie qu'il s'agit bien du formulaire déclenché par « Retirer ».
            const bouton = formulaire.querySelector('button[type="submit"]');
            const texteBouton = bouton ? bouton.textContent.trim().toLowerCase() : '';
            const action = (formulaire.getAttribute('action') || '').toLowerCase();

            if (texteBouton !== 'retirer' && !action.includes('supprimer-liste.php')) {
                return;
            }

            // Empêche la navigation classique vers supprimer-liste.php.
            evenement.preventDefault();

            const carte = formulaire.closest('.carte-contenu');
            const boutonRetirer = bouton;
            const texteInitial = boutonRetirer ? boutonRetirer.textContent : '';

            if (!carte) {
                return;
            }

            // Évite un double clic pendant le traitement de la requête.
            if (boutonRetirer) {
                boutonRetirer.disabled = true;
                boutonRetirer.textContent = 'Suppression…';
            }

            // FormData conserve notamment le champ caché id_contenu.
            fetch(formulaire.action, {
                method: 'POST',
                body: new FormData(formulaire),
                credentials: 'same-origin',
                redirect: 'follow'
            })
                .then(function (reponse) {
                    // fetch suit automatiquement la redirection éventuelle du PHP.
                    if (!reponse.ok) {
                        throw new Error('La suppression a échoué.');
                    }

                    // Déclenche l'animation seulement après une réponse HTTP valide.
                    carte.style.transition = 'opacity 300ms ease, transform 300ms ease';
                    carte.style.opacity = '0';
                    carte.style.transform = 'scale(0.96)';

                    // Retire la carte du DOM après la fin du fondu.
                    window.setTimeout(function () {
                        carte.remove();
                    }, 320);
                })
                .catch(function (erreur) {
                    // En cas d'erreur réseau, la carte reste visible et le bouton est rétabli.
                    console.error('Erreur lors du retrait de la carte :', erreur);

                    if (boutonRetirer) {
                        boutonRetirer.disabled = false;
                        boutonRetirer.textContent = texteInitial;
                    }
                });
        });
    }

    // Compatible avec un script chargé dans le <head> ou en bas de page.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialiserSuppression);
    } else {
        initialiserSuppression();
    }
})();
