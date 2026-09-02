// Récupère le bouton qui permet d'afficher la recherche.
const btnRecherche = document.getElementById("btnRecherche");

// Récupère la zone contenant le champ de recherche.
const zoneRecherche = document.getElementById("zoneRecherche");

// Récupère le formulaire de recherche.
const formRecherche = document.getElementById("formRecherche");

// Récupère la zone où les résultats seront affichés.
const resultats = document.getElementById("resultatsRecherche");


// Détecte le clic sur le bouton de recherche.
btnRecherche.addEventListener("click", function () {

    // Ajoute ou retire la classe CSS "active".
    // Cela permet généralement d'afficher ou de masquer la zone de recherche.
    zoneRecherche.classList.toggle("active");

    // Vérifie si la zone de recherche possède maintenant la classe "active".
    if (zoneRecherche.classList.contains("active")) {

        // Place automatiquement le curseur dans le champ de recherche.
        formRecherche.querySelector("input").focus();
    }
});


// Détecte l'envoi du formulaire.
formRecherche.addEventListener("submit", function (event) {

    // Empêche le rechargement automatique de la page.
    event.preventDefault();

    // Récupère toutes les données saisies dans le formulaire.
    const donnees = new FormData(formRecherche);

    // Transforme les données du formulaire en paramètres compatibles avec une URL.
    // Exemple : q=matrix&type=Film
    const parametres = new URLSearchParams(donnees);

    // Affiche un message pendant que la requête est en cours.
    resultats.innerHTML = "<p>Recherche en cours...</p>";


    // Envoie une requête vers recherche.php avec les paramètres de recherche.
    fetch("recherche.php?" + parametres.toString())

        // Transforme la réponse du serveur en texte HTML.
        .then(response => response.text())

        // Une fois les données reçues, les insère dans la zone des résultats.
        .then(data => {
            resultats.innerHTML = data;
        })

        // Exécute ce bloc si une erreur se produit.
        .catch(error => {

            // Affiche un message d'erreur à l'utilisateur.
            resultats.innerHTML =
                "<p>Une erreur est survenue pendant la recherche.</p>";
        });
});