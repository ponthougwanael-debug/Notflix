const btnRecherche = document.getElementById("btnRecherche");
const zoneRecherche = document.getElementById("zoneRecherche");
const formRecherche = document.getElementById("formRecherche");
const resultats = document.getElementById("resultatsRecherche");

btnRecherche.addEventListener("click", function () {
    zoneRecherche.classList.toggle("active");

    if (zoneRecherche.classList.contains("active")) {
        formRecherche.querySelector("input").focus();
    }
});

formRecherche.addEventListener("submit", function (event) {
    event.preventDefault();

    const donnees = new FormData(formRecherche);
    const parametres = new URLSearchParams(donnees);

    resultats.innerHTML = "<p>Recherche en cours...</p>";

    fetch("recherche.php?" + parametres.toString())
        .then(response => response.text())
        .then(data => {
            resultats.innerHTML = data;
        })
        .catch(error => {
            resultats.innerHTML =
                "<p>Une erreur est survenue pendant la recherche.</p>";
        });
});