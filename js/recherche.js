// Récupère le bouton permettant d'afficher la recherche.
const btnRecherche = document.getElementById("btnRecherche");

// Récupère la zone contenant le formulaire de recherche.
const zoneRecherche = document.getElementById("zoneRecherche");

// Récupère le formulaire de recherche.
const formRecherche = document.getElementById("formRecherche");

// Récupère la zone dans laquelle les résultats seront affichés.
const resultats = document.getElementById("resultatsRecherche");

// Ajoute un événement lors du clic sur le bouton de recherche.
btnRecherche.addEventListener("click", function () {
    // Ajoute ou retire la classe "active" sur la zone de recherche.
    zoneRecherche.classList.toggle("active");

    // Vérifie si la zone de recherche est maintenant active.
    if (zoneRecherche.classList.contains("active")) {
        // Place automatiquement le curseur dans le champ de recherche.
        formRecherche.querySelector("input").focus();
    }
});

// Ajoute un événement lors de l'envoi du formulaire.
formRecherche.addEventListener("submit", function (event) {
    // Empêche le rechargement de la page.
    event.preventDefault();

    // Récupère les données saisies dans le formulaire.
    const donnees = new FormData(formRecherche);

    // Convertit les données en paramètres utilisables dans l'URL.
    const parametres = new URLSearchParams(donnees);

    // Affiche un message pendant le chargement des résultats.
    resultats.innerHTML = "<p>Recherche en cours...</p>";

    // Envoie une requête vers recherche.php avec les paramètres saisis.
    fetch("recherche.php?" + parametres.toString())
        // Convertit la réponse reçue en texte.
        .then(response => response.text())

        // Affiche les résultats dans la page.
        .then(data => {
            resultats.innerHTML = data;
        })

        // Affiche un message si la requête échoue.
        .catch(error => {
            resultats.innerHTML =
                "<p>Une erreur est survenue pendant la recherche.</p>";
        });
});