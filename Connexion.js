//------------COOKIE PAGE USER--------------------

 // Vérifier si l'utilisateur a deja fait un choix
 document.addEventListener("DOMContentLoaded", () => {
    if (!localStorage.getItem("cookieChoix")) {
        const popup = document.getElementById("cookie-popup");
        popup.style.display = "block";
        setTimeout(() => {
            popup.classList.add('show');
        }, 100);
    }
});
  function handleCookies(accepted) {
    popup.classList.remove('show');
    popup.classList.add('hide');

    // Sauvegarde du choix
    localStorage.setItem("cookieChoix", accepted ? "accepter" : "refuser");

    // Appel au serveur
    fetch(`cookie_handler.php?action=${accepted ? "set" : "delete"}`);

    // Attendre la fin de l'animation avant de masquer le popup
    setTimeout(() => {
      popup.style.display = "none";
      popup.classList.remove('hide'); // reset classes pour un futur affichage
    }, 500);
  }