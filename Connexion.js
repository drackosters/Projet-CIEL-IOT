//------------COOKIE PAGE USER--------------------

 // Vérifier si l'utilisateur a deja fait un choix
 document.addEventListener("DOMContentLoaded", function() {
    if (!localStorage.getItem("cookieChoix")) {
        document.getElementById("cookie-popup").style.display = "block";
    }
});

function acceptCookies() {
    localStorage.setItem("cookieChoix", "accepter");
    document.getElementById("cookie-popup").style.display = "none";

    // Si accepter, stocker le cookie
    fetch('cookie_handler.php?action=set');
}

function declineCookies() {
    localStorage.setItem("cookieChoix", "refuser");
    document.getElementById("cookie-popup").style.display = "none";

    // Si refus, supprimer cookie
    fetch('cookie_handler.php?action=delete');
}