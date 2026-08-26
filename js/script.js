const champRecherche = document.querySelector('#rechercheLocale');
const cartes = document.querySelectorAll('.carte');
const aucunResultat = document.querySelector('#aucunResultat');

if (champRecherche) {
    champRecherche.addEventListener('input', () => {
        const recherche = champRecherche.value.toLowerCase().trim();
        let nombreResultats = 0;

        cartes.forEach(carte => {
            const titre = carte.querySelector('h3')?.textContent
                .toLowerCase()
                .trim() || '';

            if (titre.includes(recherche)) {
                carte.style.display = '';
                nombreResultats++;
            } else {
                carte.style.display = 'none';
            }
        });

        if (aucunResultat) {
            aucunResultat.style.display =
                nombreResultats === 0 ? 'block' : 'none';
        }
    });
}