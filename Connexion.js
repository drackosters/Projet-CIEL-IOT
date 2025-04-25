//------------COOKIE PAGE USER--------------------

document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("cookie-popup");

    if (!localStorage.getItem("cookieChoix")) {
        popup.style.display = "block";

        // Animation douce d'apparition
        setTimeout(() => {
            popup.classList.add('show');
        }, 100);
    }
});

function handleCookies(accepted) {
    const popup = document.getElementById("cookie-popup"); // ✅ Tu dois redéfinir la variable ici aussi

    popup.classList.remove('show');
    popup.classList.add('hide');

    // Sauvegarde du choix en localStorage
    localStorage.setItem("cookieChoix", accepted ? "accepter" : "refuser");

    // Appel au serveur PHP
    fetch(`cookie_handler.php?action=${accepted ? "set" : "delete"}`);

    // Animation de disparition
    setTimeout(() => {
        popup.style.display = "none";
        popup.classList.remove('hide');
    }, 500);
}
